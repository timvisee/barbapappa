<?php

namespace App\Jobs;

use App\Mail\Update\BalanceUpdateMail;
use App\Models\Community;
use App\Models\Economy;
use App\Models\EconomyMember;
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
    public function __construct(int $user_id) {
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
        // Find the user, apply it's locale to the environment
        $user = User::findOrFail($this->user_id);
        $user->applyPreferredLocale();

        // Collect user wallets
        $wallets = $user->wallets;
        if($wallets->isEmpty())
            return;

        // Get all economy members for the user wallets
        $economyMemberIds = $wallets->pluck('economy_member_id')->unique();
        $economyMembers = EconomyMember::whereIn('id', $economyMemberIds)->get();

        // Get all economies for the user wallets
        $economyIds = $economyMembers->pluck('economy_id')->unique();
        $economies = Economy::whereIn('id', $economyIds)->get();

        // Get all communities for the user wallet
        $communityIds = $economies->pluck('community_id')->unique();
        $communities = Community::whereIn('id', $communityIds)->get();

        // Build the data object with all community/economy/wallet
        // information used in the balance mail template
        $data = $communities
            ->map(function($community) use($economies, $economyMembers, $wallets) {
                $economyData = $economies
                    ->where('community_id', $community->id)
                    ->map(function($economy) use($community, $economyMembers, $wallets) {
                        // Find the economy member
                        $member = $economyMembers->where('economy_id', $economy->id)->first();

                        // Build the wallet data
                        $walletData = $wallets
                            ->where('economy_member_id', $member->id)
                            ->map(function($wallet) use($community, $economy) {
                                // Select a previous time, find it's balance
                                $previous = now()->subMonth();
                                $previousBalance = $wallet->traceBalance($previous);

                                // Build the wallet data table
                                return [
                                    'name' => $wallet->name,
                                    'balance' => $wallet->formatBalance(),
                                    'balanceHtml' => $wallet->formatBalance(BALANCE_FORMAT_COLOR),
                                    'previousBalance' => $previousBalance,
                                    'previousBalanceHtml' => $wallet->currency->format($previousBalance, BALANCE_FORMAT_COLOR),
                                    'previousPeriod' => $previous->diffForHumans(),
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
