<?php

namespace BarPay\Controllers;

use BarPay\Models\Payment;
use BarPay\Models\PaymentBunqIban;
use Illuminate\Http\Request;

// TODO: extend something else, possibly a trait
class PaymentBunqIbanController {

    public static function stepTransfer(Payment $payment, PaymentBunqIban $paymentable, $response) {
        // Get some parameters
        $reference = $payment->getReference(true, true);
        $bunqAccount = $paymentable->getBunqAccount();

        // Add SEPA QR code payload to response if can be used
        if($bunqAccount->iban != null && $bunqAccount->bic != null && $payment->currency->code == 'EUR') {
            $qr = "BCD\n";
            $qr .= "001\n";
            $qr .= "1\n";
            $qr .= "SCT\n";
            $qr .= $bunqAccount->bic . "\n";
            $qr .= $bunqAccount->account_holder . "\n";
            $qr .= $bunqAccount->iban . "\n";
            $qr .= 'EUR' . $payment->money . "\n";
            $qr .= "\n";
            $qr .= $reference;

            $response = $response->with('paymentQrPayload', $qr);
        }

        return $response
            ->with('description', $reference)
            ->with('bunqAccount', $bunqAccount);
    }

    public static function doStepTransfer(Request $request, Payment $payment, PaymentBunqIban $paymentable, $response) {
        // Validate
        $request->validate([
            'iban' => 'required|iban',
            'confirm_transfer' => 'accepted',
        ]);

        // Set the transfer time and user IBAN
        $paymentable->from_iban = $request->input('iban');
        $paymentable->transferred_at = now();
        $paymentable->save();

        // Update the payment state and step
        $payment->setState(Payment::STATE_PENDING_AUTO);

        return $response;
    }

    public static function stepReceipt(Payment $payment, PaymentBunqIban $paymentable, $response) {
        // Build time waiting string
        $timeWaiting = $paymentable
            ->transferred_at
            ->longAbsoluteDiffForHumans();

        return $response->with('timeWaiting', $timeWaiting);
    }
}
