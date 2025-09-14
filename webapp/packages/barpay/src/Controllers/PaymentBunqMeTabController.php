<?php

namespace BarPay\Controllers;

use BarPay\Models\Payment;
use BarPay\Models\PaymentBunqMeTab;
use Illuminate\Http\Request;

// TODO: extend something else, possibly a trait
class PaymentBunqMeTabController {

    public static function stepCreate(Payment $payment, PaymentBunqMeTab $paymentable, $response) {
        return $response;
    }

    public static function stepPay(Payment $payment, PaymentBunqMeTab $paymentable, $response) {
        return $response
            // Now unused because we redirect through payment.payRedirect route
            ->with('bunq_tab_url', $paymentable->bunq_tab_url);
    }

    // TODO: is this still used?
    public static function doStepPay(Request $request, Payment $payment, PaymentBunqMeTab $paymentable, $response) {
        return $response;
    }

    public static function stepReceipt(Payment $payment, PaymentBunqMeTab $paymentable, $response) {
        return $response;
    }
}
