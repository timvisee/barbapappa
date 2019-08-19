<?php

namespace App\Jobs;

use App\Mail\Update\BalanceUpdateMail;
use App\Models\Community;
use App\Models\Economy;
use App\Models\Notifications\Notification;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Send a balance update to a specific user.
 */
class SendBalanceUpdate implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    /**
     * The ID of the user to send the balance update to.
     */
    private $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id) {
        // Set queue
        $this->onQueue(Self::QUEUE);
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Find the user
        $user = User::findOrFail($this->user_id);

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
                    ->map(function($economy) use($community, $wallets) {
                        // Build the wallet data
                        $walletData = $wallets
                            ->get($economy->id)
                            ->map(function($wallet) use($community, $economy) {
                                return [
                                    'name' => $wallet->name,
                                    'balance' => $wallet->formatBalance(),
                                    'balanceHtml' => $wallet->formatBalance(BALANCE_FORMAT_COLOR),
                                    'url' => route('community.wallet.show', [
                                        'communityId' => $community->human_id,
                                        'economyId' => $economy->id,
                                        'walletId' => $wallet->id,
                                    ]),
                                    'topUpUrl' => route('community.wallet.topUp', [
                                        'communityId' => $community->human_id,
                                        'economyId' => $economy->id,
                                        'walletId' => $wallet->id,
                                    ]),
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
    }
}
