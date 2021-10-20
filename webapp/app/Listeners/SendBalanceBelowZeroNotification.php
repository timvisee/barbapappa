<?php

namespace App\Listeners;

use App\Events\WalletBalanceChange;
use App\Jobs\SendBalanceBelowZeroMail;

class SendBalanceBelowZeroNotification {

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() { }

    /**
     * Handle the event.
     *
     * @param  WalletBalanceChange  $event
     * @return void
     */
    public function handle(WalletBalanceChange $event) {
        // Balance must drop below zero
        if($event->before->amount < 0 || $event->after->amount >= 0)
            return;

        // User must have these notifications enabled
        $user = $event->wallet->economyMember->user;
        if($user == null || !$user->notify_low_balance)
            return;

        // Dispatch job to send balance below zero mail
        SendBalanceBelowZeroMail::dispatch($event->wallet->id);
    }
}
