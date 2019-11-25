<?php

namespace BarPay\Controllers;

use App\Helpers\ValidationDefaults;
use BarPay\Models\Payment;
use BarPay\Models\PaymentManualIban;
use Illuminate\Http\Request;

// TODO: extend something else, possibly a trait
class PaymentManualIbanController {

    public static function stepTransfer(Payment $payment, PaymentManualIban $paymentable, $response) {
        // Get some parameters
        $reference = $payment->getReference(true, true);

        // Add SEPA QR code payload to response if can be used
        if($paymentable->to_iban != null && $paymentable->to_bic != null && $payment->currency->code == 'EUR') {
            $qr = "BCD\n";
            $qr .= "001\n";
            $qr .= "1\n";
            $qr .= "SCT\n";
            $qr .= $paymentable->to_bic . "\n";
            $qr .= $paymentable->to_account_holder . "\n";
            $qr .= $paymentable->to_iban . "\n";
            $qr .= 'EUR' . $payment->money . "\n";
            $qr .= "\n";
            $qr .= $reference;

            $response = $response->with('paymentQrPayload', $qr);
        }

        return $response->with('description', $reference);
    }

    public static function doStepTransfer(Request $request, Payment $payment, PaymentManualIban $paymentable, $response) {
        // Validate
        $request->validate([
            'iban' => 'required|iban',
            'confirm_transfer' => 'accepted',
        ]);

        // Set the transfer time and user IBAN
        $paymentable->from_iban = $request->input('iban');
        $paymentable->transferred_at = now();
        $paymentable->save();

        // Update the payment state
        $payment->setState(Payment::STATE_PENDING_AUTO);

        return $response;
    }

    public static function stepTransferring(Payment $payment, PaymentManualIban $paymentable, $response) {
        // Build time left string
        $timeLeft = $paymentable
            ->transferred_at
            ->addSeconds(PaymentManualIban::TRANSFER_WAIT)
            ->shortAbsoluteDiffForHumans(2);

        return $response->with('timeLeft', $timeLeft);
    }

    public static function stepReceipt(Payment $payment, PaymentManualIban $paymentable, $response) {
        // Build time waiting string
        $timeWaiting = $paymentable
            ->transferred_at
            ->addSeconds(PaymentManualIban::TRANSFER_WAIT)
            ->shortAbsoluteDiffForHumans();

        return $response->with('timeWaiting', $timeWaiting);
    }

    public static function approveStepReceipt(Payment $payment, PaymentManualIban $paymentable, $response) {
        // Check whether delay is allowed
        $allowDelay = $paymentable ->transferred_at
            >= now()->subSeconds(PaymentManualIban::TRANSFER_DELAY_MAX);

        // Respond
        return $response
            ->with('description', $payment->getReference(true, true))
            ->with('allowDelay', $allowDelay);
    }

    public static function doApproveStepReceipt(Request $request, Payment $payment, PaymentManualIban $paymentable, $response) {
        // Gather some facts, the current time and the current user
        $now = now();
        $user = barauth()->getUser()->id;
        $action = $request->input('choice');

        // Validate
        $request->validate([
            'choice' => 'required|in:approve,delay,reject',
            'confirm' => 'accepted',
        ]);

        // Update the checked time
        $paymentable->checked_at = $now;
        $paymentable->assessor_id = $user;

        // Handle the choice
        switch($action) {
        case 'approve':
            // Update the settle time, settle the payment
            $paymentable->settled_at = $now;
            $payment->settle(Payment::STATE_COMPLETED, true);
            break;

        case 'delay':
            $payment->setState(Payment::STATE_PENDING_AUTO, true);
            break;

        case 'reject':
            // Update the settle time, settle the payment
            $paymentable->settled_at = $now;
            $payment->settle(Payment::STATE_REJECTED, true);
            break;

        default:
            throw new \Exception('Unknown approval action');
        }

        // Save the paymentable
        $paymentable->save();

        // Add a status message and return
        return $response
            ->with('success', __('barpay::payment.manualiban.actionMessage.' . $action));
    }
}
