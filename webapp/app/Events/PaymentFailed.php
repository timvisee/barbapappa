<?php

namespace App\Events;

use BarPay\Models\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    // TODO: switch to Payment once mutations properly serialize
    public $payment_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Payment $payment) {
        $this->payment_id = $payment->id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('channel-name');
    }
}
