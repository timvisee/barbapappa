<?php

namespace App\Jobs;

use BarPay\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Expire the payment with the given ID.
 */
class ExpirePayment implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    /**
     * The ID of the payment to expire.
     */
    private $payment_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $payment_id) {
        // Set queue
        $this->onQueue(Self::QUEUE);
        $this->payment_id = $payment_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $self = $this;
        DB::transaction(function() use($self) {
            // Find the payment, skip if not processing
            $payment = Payment::find($self->payment_id);
            if($payment != null && !$payment->isInProgress())
                return;

            // Expire the payment
            $payment->setState(Payment::STATE_REVOKED);
        });
    }
}
