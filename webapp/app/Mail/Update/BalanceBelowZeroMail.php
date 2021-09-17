<?php

namespace App\Mail\Update;

use App\Models\Wallet;
use App\Mail\PersonalizedEmail;
use App\Utils\EmailRecipient;
use Illuminate\Mail\Mailable;

class BalanceBelowZeroMail extends PersonalizedEmail {

    /**
     * Email subject.
     */
    const SUBJECT = 'mail.update.belowZero.subject';

    /**
     * Email view.
     */
    const VIEW = 'mail.update.belowZero';

    /**
     * The worker queue to put this mailable on.
     */
    const QUEUE = 'low';

    /**
     * The wallet this message is for.
     */
    private $wallet_id;

    /**
     * Constructor.
     *
     * @param EmailRecipient|EmailRecipient[] $recipient Email recipient.
     * @param array Array with wallet/economy data for the user.
     */
    public function __construct($recipient, $wallet) {
        parent::__construct($recipient, self::SUBJECT);
        $this->wallet_id = $wallet->id;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        // Get wallet, ensure it exists
        $wallet = Wallet::find($this->wallet_id);
        if($wallet == null)
            return null;

        $economy = $wallet->economyMember->economy;
        $community = $economy->community;

        return parent::build()
            ->markdown(self::VIEW)
            ->with('wallet', $wallet)
            ->with('economy', $economy)
            ->with('community', $community)
            ->with('walletUrl', route('community.wallet.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'walletId' => $wallet->id,
            ]))
            ->with('transactionsUrl', route('community.wallet.transactions', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'walletId' => $wallet->id,
            ]))
            ->with('topUpUrl', route('community.wallet.topUp', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'walletId' => $wallet->id,
            ]));
    }

    /**
     * Get the worker queue to put this mailable on.
     * @return string
     */
    protected function getWorkerQueue() {
        return self::QUEUE;
    }
}
