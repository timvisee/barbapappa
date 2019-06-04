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
        $payments = $user->payments()->latest('updated_at');

        return view('payment.index')
            ->with('inProgress', $payments->inProgress(true)->get())
            ->with('settled', $payments->inProgress(false)->get());
    }

    /**
     * Show a user payment.
     *
     * @return Response
     */
    public function show($paymentId) {
        // TODO: do some advanced permission checking here!

        // Get the payment
        $payment = Payment::findOrFail($paymentId);

        // // Check permission
        // // TODO: check this permission in middleware, redirect to login
        // if(!Self::hasPermission($transaction))
        //     return response(view('noPermission'));

        return view('payment.show')
            ->with('payment', $payment);
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

        // If the payment is not in progress anymore, redirect to show page
        if(!$payment->isInProgress())
            return redirect()->route('payment.show', ['paymentId' => $payment->id]);

        // // Check permission
        // // TODO: check this permission in middleware, redirect to login
        // if(!Self::hasPermission($transaction))
        //     return response(view('noPermission'));

        // Build the response
        $response = view('payment.pay')
            ->with('payment', $payment)
            ->with('steps', $payment->getStepsData())
            ->with('stepView', $paymentable->getStepView());

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

        // Run through paymentable controller action as well, return
        return ($paymentable::CONTROLLER)::{$paymentable->getStepAction('do')}($request, $payment, $paymentable, $response);
    }
}
