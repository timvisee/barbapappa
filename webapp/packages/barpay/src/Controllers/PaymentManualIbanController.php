<?php

namespace BarPay\Controllers;

use App\Helpers\ValidationDefaults;
use BarPay\Models\Payment;
use BarPay\Models\PaymentManualIban;
use Illuminate\Http\Request;

// TODO: extend something else, possibly a trait
class PaymentManualIbanController {

    public static function stepTransfer(Payment $payment, PaymentManualIban $paymentable, $response) {
        return $response;
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

        return $response;
    }

    public static function stepTransferring(Payment $payment, PaymentManualIban $paymentable, $response) {
        // Build time left string
        $timeLeft = $paymentable
            ->transferred_at
            ->addSeconds(PaymentManualIban::TRANSFER_WAIT)
            ->longAbsoluteDiffForHumans(2);

        return $response->with('timeLeft', $timeLeft);
    }
}
