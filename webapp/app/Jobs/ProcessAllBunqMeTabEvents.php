<?php

namespace App\Jobs;

use BarPay\Models\Payment;
use BarPay\Models\PaymentBunqMeTab;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Process all bunq me tab payments that are still in progress.
 */
class ProcessAllBunqMeTabEvents implements ShouldQueue {

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
            new RateLimited('bunq-api'),
            (new WithoutOverlapping())
                // Release exclusive lock after a day (failure)
                ->expireAfter(24 * 60 * 60)
                ->dontRelease(),
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Find all in-progress bunq me tab payments with a tab ID
        PaymentBunqMeTab::whereNotNull('bunq_tab_id')
            ->whereHas('payment', function($query) {
                $query->inProgress();
            })
            ->with('payment.service.serviceable.bunqAccount')
            ->get()
            ->each(function($paymentable, $i) {
                $account = $paymentable->getBunqAccount();

                // If bunq account got unlinked, abort payment on our end
                if(is_null($account)) {
                    \Log::error("bunq me tab: marking payment as failed because connection to bunq account is lost (paymentable id: {$paymentable->id})");

                    $payment = $paymentable->payment;
                    if($payment->isInProgress()) {
                        DB::transaction(function() use($payment) {
                            $payment->settle(Payment::STATE_FAILED, true);
                        });
                    }
                    return;
                }

                ProcessBunqBunqMeTabEvent::dispatch($account, $paymentable->bunq_tab_id)
                    ->delay(now()->addMinutes($i));
            });
    }

    public function retryUntil() {
        // Matches interval in \App\Console\Kernel::schedule
        return now()->addHours(12);
    }
}
