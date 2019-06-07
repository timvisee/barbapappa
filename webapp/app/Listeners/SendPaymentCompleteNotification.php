<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Mail\Email\Payment\Completed;
use BarPay\Models\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendPaymentCompleteNotification implements ShouldQueue {

    public $queue = 'normal';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Event  $event
     * @return void
     */
    public function handle(PaymentCompleted $event) {
        // Gather facts
        $payment = Payment::findOrFail($event->payment_id);
        $user = $payment->user;

        // Create the mailable for the settlement, send the mailable
        Mail::send(new Completed($user->buildEmailRecipients(), $payment));
    }
}
