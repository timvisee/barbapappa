<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Mutation;
use App\Models\Transaction;
use App\Perms\Builder\Config as PermsConfig;
use App\Perms\CommunityRoles;
use BarPay\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Validator;

class PaymentController extends Controller {

    /**
     * Payment index page.
     * Show a list of payments for the current user.
     *
     * @return Response
     */
    public function index(Request $request) {
        // Get the user, community, find the products
        $user = barauth()->getUser();

        // Get payments in different stages
        $requireUserAction = $user
            ->payments()
            ->inProgress(true)
            ->requireUserAction()
            ->latest('updated_at')
            ->get();
        $inProgress = $user
            ->payments()
            ->inProgress(true)
            ->latest('updated_at')
            ->whereNotIn('id', $requireUserAction->pluck('id'))
            ->get();
        $settled = $user
            ->payments()
            ->inProgress(false)
            ->latest('updated_at')
            ->get();

        // Check whether there are 
        $requireCommunityAction = Payment::canManage()
            ->requireCommunityAction()
            ->limit(1)
            ->count() > 0;

        return view('payment.index')
            ->with('requireUserAction', $requireUserAction)
            ->with('inProgress', $inProgress)
            ->with('settled', $settled)
            ->with('requireCommunityAction', $requireCommunityAction);
    }

    /**
     * Show a user payment.
     *
     * @return Response
     */
    public function show($paymentId) {
        // TODO: do some advanced permission checking here!

        // Get the payment, find it's transaction
        $payment = Payment::findOrFail($paymentId);
        $transaction = $payment->findTransaction();

        // // Check permission
        // // TODO: check this permission in middleware, redirect to login
        // if(!Self::hasPermission($transaction))
        //     return response(view('noPermission'));

        // Force update the payment state
        $payment->updateState();

        return view('payment.show')
            ->with('payment', $payment)
            ->with('transaction', $transaction);
    }

    /**
     * Show the payment progression page.
     *
     * @return Response
     */
    public function pay($paymentId) {
        // TODO: do some advanced permission checking here!

        // Get the payment and paymentable
        $payment = Payment::findOrFail($paymentId);
        $paymentable = $payment->paymentable;

        // // Check permission
        // // TODO: check this permission in middleware, redirect to login
        // if(!Self::hasPermission($transaction))
        //     return response(view('noPermission'));

        // Force update the payment state
        $payment->updateState();

        // If the payment is not in progress anymore, redirect to show page
        if(!$payment->isInProgress())
            return redirect()->route('payment.show', ['paymentId' => $payment->id]);

        // Build the response
        $response = view('payment.pay')
            ->with('payment', $payment)
            ->with('steps', $payment->getStepsData())
            ->with('stepView', $paymentable->getStepPayView());

        // Run through paymentable controller action as well, return
        return ($paymentable::CONTROLLER)::{$paymentable->getStepAction()}($payment, $paymentable, $response);
    }

    /**
     * Show the payment progression page.
     *
     * @return Response
     */
    public function doPay(Request $request, $paymentId) {
        // TDO: do some advanced permission checking here!

        // Get the payment and paymentable
        $payment = Payment::findOrFail($paymentId);
        $paymentable = $payment->paymentable;

        // If the payment is not in progress anymore, redirect to show page
        if(!$payment->isInProgress())
            return redirect()->route('payment.show', ['paymentId' => $payment->id]);

        // // Check permission
        // // TODO: check this permission in middleware, redirect to login
        // if(!Self::hasPermission($transaction))
        //     return response(view('noPermission'));

        // Build the response
        $response = redirect()->route('payment.pay', ['paymentId' => $payment->id]);

        // Run paymentable specific action in a transaction, then return
        DB::transaction(function() use($request, $payment, $paymentable, &$response) {
            $response = ($paymentable::CONTROLLER)
                ::{$paymentable->getStepAction('do')}
                ($request, $payment, $paymentable, $response);
        });
        return $response;
    }

    /**
     * Show the payment cancellation page.
     *
     * @return Response
     */
    public function cancel($paymentId) {
        // TODO: do some advanced permission checking here!

        // Get the payment
        $payment = Payment::findOrFail($paymentId);

        // // Check permission
        // // TODO: check this permission in middleware, redirect to login
        // if(!Self::hasPermission($transaction))
        //     return response(view('noPermission'));

        // We must be able to cancel
        if(!$payment->canCancel())
            return redirect()
                ->route('payment.show', ['paymentId' => $payment->id])
                ->with('error', __('barpay::misc.cannotCancelPaymentCurrently'));

        // Build and return the response
        return view('payment.cancel')->with('payment', $payment);
    }

    /**
     * Show the payment cancellation page.
     *
     * @return Response
     */
    public function doCancel(Request $request, $paymentId) {
        // TDO: do some advanced permission checking here!

        // Get the payment and paymentable
        $payment = Payment::findOrFail($paymentId);

        // // Check permission
        // // TODO: check this permission in middleware, redirect to login
        // if(!Self::hasPermission($transaction))
        //     return response(view('noPermission'));

        // We must be able to cancel
        if(!$payment->canCancel())
            return redirect()
                ->route('payment.show', ['paymentId' => $payment->id])
                ->with('error', __('barpay::misc.cannotCancelPaymentCurrently'));

        // Validate
        $request->validate([
            'confirm' => 'accepted',
        ]);

        // Revoke the payment
        DB::transaction(function() use($payment) {
            $payment->settle(Payment::STATE_REVOKED);
        });

        // Build and returnthe response
        return redirect()
            ->route('payment.index')
            ->with('success', __('pages.payments.paymentCancelled'));
    }

    /**
     * Payment approval list page.
     * Show a list of payments that the current user can approve.
     *
     * @return Response
     */
    public function approveList(Request $request) {
        // Get the user, community, find the products
        $user = barauth()->getUser();

        // Get all payments to approve, show earliest first
        $payments = Payment::canManage()
            ->requireCommunityAction()
            ->oldest('updated_at');

        return view('payment.approveList')
            ->with('payments', $payments->get());
    }

    /**
     * Show payment approval page.
     *
     * @return Response
     */
    public function approve($paymentId) {
        // TODO: do some advanced permission checking here!

        // Get the payment
        $payment = Payment::requireCommunityAction()->findOrFail($paymentId);
        $paymentable = $payment->paymentable;

        // If the payment is not in progress anymore, redirect to show page
        if(!$payment->isInProgress())
            return redirect()->route('payment.show', ['paymentId' => $payment->id]);

        // // Check permission
        // // TODO: check this permission in middleware, redirect to login
        // if(!Self::hasPermission($transaction))
        //     return response(view('noPermission'));

        // Build the response
        $response = view('payment.approve')
            ->with('payment', $payment)
            ->with('stepView', $paymentable->getStepApproveView());

        // Run through paymentable controller action as well, return
        return ($paymentable::CONTROLLER)::{$paymentable->getStepAction('approve')}($payment, $paymentable, $response);
    }

    /**
     * Approve the payment.
     *
     * @return Response
     */
    public function doApprove(Request $request, $paymentId) {
        // TODO: do some advanced permission checking here!

        // Get the payment
        $payment = Payment::requireCommunityAction()->findOrFail($paymentId);
        $paymentable = $payment->paymentable;

        // If the payment is not in progress anymore, redirect to show page
        if(!$payment->isInProgress())
            return redirect()->route('payment.show', ['paymentId' => $payment->id]);

        // // Check permission
        // // TODO: check this permission in middleware, redirect to login
        // if(!Self::hasPermission($transaction))
        //     return response(view('noPermission'));

        // Build the response
        $response = redirect()->route('payment.approveList');

        // Run paymentable specific action in a transaction, then return
        DB::transaction(function() use($request, $payment, $paymentable, &$response) {
            $response = ($paymentable::CONTROLLER)
                ::{$paymentable->getStepAction('doApprove')}
                ($request, $payment, $paymentable, $response);
        });
        return $response;
    }
}
