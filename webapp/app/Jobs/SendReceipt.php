<?php

namespace App\Jobs;

use App\Mail\Update\ReceiptMail;
use App\Models\Community;
use App\Models\Economy;
use App\Models\EconomyMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Send a receipt to a specific user.
 */
class SendReceipt implements ShouldQueue {

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
     * The period start.
     */
    private ?Carbon $from;

    /**
     * The period end.
     */
    private Carbon $to;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $user_id, ?Carbon $from = null, ?Carbon $to = null) {
        // Set queue
        $this->onQueue(Self::QUEUE);
        $this->user_id = $user_id;
        $this->from = $from;
        $this->to = $to ?? now();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Find the user, apply it's locale to the environment
        $user = User::findOrFail($this->user_id);
        $locale = $user->preferredLocale();
        if(!empty($locale))
            set_env_locale($locale);

        // Find user email recipients, skip sending if user does not have any
        $recipients = $user->buildEmailRecipients();
        if($recipients->isEmpty())
            return;

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

        // Get all communities for the user wallets
        $communityIds = $economies->pluck('community_id')->unique();
        $communities = Community::whereIn('id', $communityIds)->get();

        // Build the data object with all community/economy/wallet
        // information used in the balance mail template
        $self = $this;
        $data = $communities
            ->map(function($community) use($self, $economies, $economyMembers, $wallets) {
                $economyData = $economies
                    ->where('community_id', $community->id)
                    ->map(function($economy) use($self, $community, $economyMembers, $wallets) {
                        // Find the economy member
                        $member = $economyMembers->where('economy_id', $economy->id)->first();

                        // Build the wallet data
                        $walletData = $wallets
                            ->where('economy_member_id', $member->id)
                            ->map(function($wallet) use($self, $community, $economy) {
                                // Build the wallet data table
                                return [
                                    'name' => $wallet->name,
                                    'receipt' => $wallet->getReceiptData(true, $self->from, $self->to, true),
                                    'balance' => $wallet->formatBalance(),
                                    'balanceHtml' => $wallet->formatBalance(BALANCE_FORMAT_COLOR),
                                    'isNegative' => $wallet->balance < 0.0,
                                    'url' => route('community.wallet.show', [
                                        'communityId' => $community->human_id,
                                        'economyId' => $economy->id,
                                        'walletId' => $wallet->id,
                                    ]),
                                    'statsUrl' => route('community.wallet.stats', [
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
                            })
                            ->filter(function($wallet) {
                                return $wallet['receipt'] != null;
                            });

                        // Drop if wallets have no receipt
                        if($walletData->isEmpty())
                            return null;

                        // Build economy object with wallets data
                        return [
                            'name' => $economy->name,
                            'wallets' => $walletData,
                        ];
                    })
                    ->filter(function($economy) {
                        return $economy != null;
                    });

                // Drop if economy has no data
                if($economyData->isEmpty())
                    return null;

                // Build community object with economy data
                return [
                    'name' => $community->name,
                    'economies' => $economyData,
                ];
            })
            ->filter(function($community) {
                return $community != null;
            })
            ->toArray();

        // We must have something to send
        if(empty($data))
            return;

        // Send the receipt mail
        Mail::send(new ReceiptMail($recipients, $data));
    }

    public function retryUntil() {
        // After one week it really is to late to still send an update
        return now()->addWeek();
    }
}
