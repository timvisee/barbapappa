<?php

namespace App\Jobs;

use BarPay\Models\Payment;
use BarPay\Models\PaymentManualIban;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Update the states payments are in.
 *
 * For some payments, it's state may change after a period of time because it
 * has to be verified by a community administrator. This job ensures these
 * states are changed.
 */
class UpdatePaymentStates implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {
        // Set queue
        $this->onQueue(Self::QUEUE);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Update steps for all in-progress payments
        Payment::inProgress()
            ->each(function($payment) {
                // Set state if community admin must check payment
                if($payment->paymentable_type == PaymentManualIban::class)
                    if($payment->paymentable->checkRequiresCommunityAction())
                        $payment->setState(Payment::STATE_PENDING_COMMUNITY);
            });
    }
}