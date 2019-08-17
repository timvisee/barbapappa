<?php

namespace App\Jobs;

use App\Mail\Update\BalanceUpdateMail;
use App\Models\Community;
use App\Models\Economy;
use App\Models\EmailHistory;
use App\Models\Notifications\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Send an update to all users each month, with their current wallet balance.
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
                    ->whereRaw('wallets.user_id = users.id')
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
            // Collect wallet balances for user, group by economy
            $wallets = $user
                ->wallets
                ->groupBy('economy_id');

            // Get all economies for the user wallets
            $economyIds = $wallets->keys();
            $economies = Economy::whereIn('id', $economyIds)->get();

            // Get all communities for the user wallet
            $communityIds = $economies->pluck('community_id')->unique();
            $communities = Community::whereIn('id', $communityIds)->get();

            // Build the data object with all community/economy/wallet
            // information used in the balance mail template
            $data = $communities
                ->map(function($community) use($economies, $wallets) {
                    $economyData = $economies
                        ->where('community_id', $community->id)
                        ->map(function($economy) use($wallets) {
                            // Build the wallet data
                            $walletData = $wallets
                                ->get($economy->id)
                                ->map(function($wallet) {
                                    return [
                                        'name' => $wallet->name,
                                        'balance' => $wallet->formatBalance(),
                                        'balanceHtml' => $wallet->formatBalance(BALANCE_FORMAT_COLOR),
                                    ];
                                });

                            // Build economy object with wallets data
                            return [
                                'name' => $economy->name,
                                'wallets' => $walletData,
                            ];
                        });

                    // Build community object with economy data
                    return [
                        'name' => $community->name,
                        'economies' => $economyData,
                    ];
                })->toArray();

            // Send the balance update mail
            Mail::send(
                new BalanceUpdateMail($user->buildEmailRecipients(), $data)
            );

            // Update email history time
            EmailHistory::updateOrCreate([
                'user_id' => $user->id,
                'type' => EmailHistory::TYPE_BALANCE_UPDATE,
            ], [
                'last_at' => now(),
            ]);
        });
    }
}
