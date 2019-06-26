<?php

namespace App\Jobs;

use BarPay\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Update the step payments are in.
 *
 * Sometimes the current step as set in payment models gets out of sync with the
 * actual step a payment is in. This job ensures all payments have their current
 * step set to the proper value.
 */
class UpdatePaymentSteps implements ShouldQueue {

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
                $payment->updateStep();
            });

        // Update steps for all settled payments, having a non-null step
        Payment::inProgress(false)
            ->each(function($payment) {
                $payment->updateStep();
            });
    }
}
