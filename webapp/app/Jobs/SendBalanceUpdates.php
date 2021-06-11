<?php

namespace App\Jobs;

use App\Models\EmailHistory;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
     * Time in seconds for the first update email to be send to each user.
     */
    const UPDATE_FIRST_DELAY = 60 * 60 * 24 * 3;

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
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware() {
        return [
            (new WithoutOverlapping())
                // Release exclusive lock after a day (failure)
                ->expireAfter(24 * 60 * 60)
                ->dontRelease()
        ];
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
                    ->from('wallet')
                    ->join('economy_member', 'economy_member.id', '=', 'wallet.economy_member_id')
                    ->whereRaw('economy_member.user_id = user.id')
                    ->where('balance', '!=', 0);
            })
            ->whereNotExists(function($query) {
                $query->selectRaw('1')
                    ->from('email_history')
                    ->whereRaw('email_history.user_id = user.id')
                    ->whereNotNull('last_at')
                    ->where('last_at', '>=', now()
                            ->subSeconds(Self::UPDATE_INTERVAL)
                            ->subSeconds(Self::UPDATE_INTERVAL_PLAY)
                    );
            })
            ->get();

        // Send an update to each listed user
        $users->each(function($user) {
            DB::transaction(function() use($user) {
                // Find the email history entry
                $email_history = EmailHistory::where('user_id', $user->id)
                    ->where('type', EmailHistory::TYPE_BALANCE_UPDATE)
                    ->first();

                // Create new entry if non existant
                // This sets a new last email time to one month back, adding the
                // first update delay. This ensures that the user is now
                // properly tracked for balance update emails.
                if($email_history == null) {
                    $email_history = new EmailHistory();
                    $email_history->user_id = $user->id;
                    $email_history->type = EmailHistory::TYPE_BALANCE_UPDATE;
                    $email_history->last_at = now()
                            ->subSeconds(Self::UPDATE_INTERVAL)
                            ->addSeconds(Self::UPDATE_FIRST_DELAY);
                    $email_history->save();
                    return;
                }

                // Update last time in existing entity
                $email_history->last_at = now();
                $email_history->save();

                // Schedule a job to send the balance update for the user
                SendBalanceUpdate::dispatch($user->id);
            });
        });
    }

    public function retryUntil() {
        // Matches interval in \App\Console\Kernel::schedule
        return now()->addHour();
    }
}
