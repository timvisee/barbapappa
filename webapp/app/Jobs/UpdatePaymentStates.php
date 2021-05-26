<?php

namespace App\Jobs;

use BarPay\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
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
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware() {
        return [
            (new WithoutOverlapping())
                // Release exclusive lock after 2* interval in \App\Console\Kernel::schedule (failure)
                ->expireAfter(2 * 15 * 60)
                ->dontRelease()
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Update state of payments waiting for community action
        Payment::inProgress()
            ->requireCommunityAction()
            ->where('state', '!=', Payment::STATE_PENDING_COMMUNITY)
            ->each(function($payment) {
                $payment->setState(Payment::STATE_PENDING_COMMUNITY);
            });
    }

    public function retryUntil() {
        // Matches interval in \App\Console\Kernel::schedule
        return now()->addMinutes(15);
    }
}
