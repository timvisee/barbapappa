<?php

namespace BarPay\Controllers;

use App\Helpers\ValidationDefaults;
use BarPay\Models\Payment;
use BarPay\Models\PaymentBunqmeTab;
use Illuminate\Http\Request;

// TODO: extend something else, possibly a trait
class PaymentBunqmeTabController {

    public static function stepCreate(Payment $payment, PaymentBunqmeTab $paymentable, $response) {
        return $response;
    }

    public static function stepPay(Payment $payment, PaymentBunqmeTab $paymentable, $response) {
        // TODO: pass bunqme tab link to response

        return $response;
    }

    // TODO: is this still used?
    public static function doStepPay(Request $request, Payment $payment, PaymentBunqmeTab $paymentable, $response) {
        // Validate
        $request->validate([
            'confirm_transfer' => 'accepted',
        ]);

        // Set the transfer time and user IBAN
        $paymentable->transferred_at = now();
        $paymentable->save();

        return $response;
    }

    public static function stepReceipt(Payment $payment, PaymentBunqmeTab $paymentable, $response) {
        // Build time waiting string
        $timeWaiting = $paymentable
            ->transferred_at
            ->longAbsoluteDiffForHumans();

        return $response->with('timeWaiting', $timeWaiting);
    }
}
