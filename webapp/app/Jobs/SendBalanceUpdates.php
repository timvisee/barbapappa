<?php

namespace App\Jobs;

use App\Mail\Update\BalanceUpdateMail;
use App\Models\EmailHistory;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Check for each user whether a new balance update should be sent, and in that
 * case, schedule a job to do so.
 */
class SendBalanceUpdates implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    /**
     * Interval in seconds between each update email for a single user.
     */
    const UPDATE_INTERVAL = 60 * 60 * 24 * 30;

    /**
     * Allowed update interval play in seconds.
     *
     * This is to fix any delays in sending emails. Time between emails usually
     * differs slightly and isn't sent at the exact specified `UPDATE_INTERVAL`.
     * This helps to prevent the usual update time from shifting because of this.
     *
     * Effectively, the minimum time between email updates is
     * `UPDATE_INTERVAL - `UPDATE_INTERVAL_PLAY`.
     * Usually half of the scheduled task time interval is used.
     */
    const UPDATE_INTERVAL_PLAY = 60 * 30;

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
        // List all users having a wallet with non-zero balance, that did not
        // receieve a recent balance update email
        $users = User::whereExists(function($query) {
                $query->selectRaw('1')
                    ->from('wallets')
                    ->join('economy_member', 'economy_member.id', '=', 'wallets.economy_member_id')
                    ->whereRaw('economy_member.user_id = users.id')
                    ->where('balance', '<>', 0);
            })
            ->whereNotExists(function($query) {
                $query->selectRaw('1')
                    ->from('email_history')
                    ->whereRaw('email_history.user_id = users.id')
                    ->whereNull('last_at')
                    ->orWhere('last_at', '>=', now()->subSeconds(Self::UPDATE_INTERVAL - Self::UPDATE_INTERVAL_PLAY));
            })
            ->get();

        // Send an update to each listed user
        $users->each(function($user) {
            DB::transaction(function() use($user) {
                // Schedule a job to send the balance update for the user
                SendBalanceUpdate::dispatch($user->id);

                // Update email history time
                EmailHistory::updateOrCreate([
                    'user_id' => $user->id,
                    'type' => EmailHistory::TYPE_BALANCE_UPDATE,
                ], [
                    'last_at' => now(),
                ]);
            });
        });
    }
}
