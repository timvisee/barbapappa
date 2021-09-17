<?php

namespace App\Jobs;

use App\Mail\Update\BalanceBelowZeroMail;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Send a balance below zero mail to a specific user.
 */
class SendBlanaceBelowZeroMail implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    /**
     * The ID of the wallet to send the notification for.
     */
    private $wallet_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $wallet_id) {
        // Set queue
        $this->onQueue(Self::QUEUE);
        $this->wallet_id = $wallet_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // TODO: ensure user wants to receive these mails
        // TODO: ensure wallet has balance below zero

        // Get wallet, must exist
        $wallet = Wallet::find($this->wallet_id);
        if($wallet == null)
            return null;

        // Wallet balance must be below zero
        if($wallet->balance >= 0)
            return null;

        // Get wallet user, skip if no user account is linked
        $economyMember = $wallet->economyMember;
        $user = $economyMember->user;
        if($user == null)
            return null;

        // Apply user locale to environment
        $locale = $user->preferredLocale();
        if(!empty($locale))
            set_env_locale($locale);

        // Find user email recipients, skip sending if user does not have any
        $recipients = $user->buildEmailRecipients();
        if($recipients->isEmpty())
            return;

        // TODO: build data object for mailable, instead of providing wallet

        // Send the balance update mail
        Mail::send(new BalanceBelowZeroMail($recipients, $wallet));
    }

    public function retryUntil() {
        // After a week it really is to late to still send an update
        return now()->addWeek();
    }
}
