<?php

namespace App\Jobs;

use App\Jobs\ExpirePayment;
use BarPay\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Check for payments that should expire, and schedule a job to expire them.
 */
class ExpirePayments implements ShouldQueue {

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
        // List all payments to expire
        $payments = Payment::toExpire()->get();

        // Schedule a job to expire each payment
        $payments->each(function($payment) {
            ExpirePayment::dispatch($payment->id);
        });
    }
}
