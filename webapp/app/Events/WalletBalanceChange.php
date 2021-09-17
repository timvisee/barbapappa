<?php

namespace App\Events;

use App\Models\Wallet;
use App\Utils\MoneyAmount;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalletBalanceChange {

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Wallet with changed balance.
     * @type Wallet
     */
    public $wallet;

    /**
     * Balance before change
     * @type MoneyAmount.
     */
    public $before;

    /**
     * Balance after change
     * @type MoneyAmount.
     */
    public $after;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Wallet $wallet, MoneyAmount $before, MoneyAmount $after) {
        $this->wallet = $wallet;
        $this->before = $before;
        $this->after = $after;
    }
}
