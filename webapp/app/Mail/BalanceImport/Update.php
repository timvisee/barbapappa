<?php

namespace App\Mail\BalanceImport;

use App\Mail\PersonalizedEmail;
use App\Models\BalanceImportChange;
use App\Utils\EmailRecipient;
use Illuminate\Mail\Mailable;

class Update extends PersonalizedEmail {

    /**
     * Email subject.
     */
    const SUBJECT = 'mail.balanceImport.update.subject';

    /**
     * The view to use.
     */
    const VIEW = 'mail.balanceImport.update';

    /**
     * The worker queue to put this mailable on.
     */
    const QUEUE = 'low';

    /**
     * The balance import change.
     */
    private $change;

    /**
     * An optional extra message.
     */
    private $message;

    /**
     * Bar to invite the user to if not joined yet.
     */
    private $invite_to_bar;

    /**
     * Bar this import change is for.
     */
    private $bar;

    /**
     * Related mutation for this change.
     */
    private $mutation;

    /**
     * Related user wallet for this change which it is applied to.
     */
    private $wallet;

    /**
     * The current balance.
     */
    private $balance;

    /**
     * Balance change.
     */
    private $balanceChange;

    /**
     * Constructor.
     *
     * @param EmailRecipient[] $recipients A list of email recipients.
     * @param BalanceImportChange $change The balance import change.
     * @param string|null $message An extra message.
     * @param Bar|null $invite_to_bar Bar to invite user to.
     * @param Bar|null $bar Bar this import change is for.
     * @param Mutation|null $mutation The mutation for this change if there is any.
     * @param Wallet|null $wallet The related user wallet if there is any.
     * @param MoneyAmount $balance The current balance.
     * @param MoneyAmount $balanceChange The balance change.
     */
    public function __construct($recipients, BalanceImportChange $change, $message, $invite_to_bar, $bar, $mutation, $wallet, $balance, $balanceChange) {
        // Construct the parent
        parent::__construct($recipients, self::SUBJECT);

        $this->change = $change;
        $this->message = $message;
        $this->invite_to_bar = $invite_to_bar;
        $this->bar = $bar;
        $this->mutation = $mutation;
        $this->wallet = $wallet;
        $this->balance = $balance;
        $this->balanceChange = $balanceChange;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        // Get change/balance update details
        $alias = $this->change->alias;
        $event = $this->change->event;
        $system = $event->system;
        $user = $alias->user()->first();
        $user_name = $user != null ? $user->first_name : $alias->name;

        // Build dynamic subtitle
        $economy = $system->economy;
        $subtitle = $this->bar != null
            ? __('mail.balanceImport.update.subtitleWithBar', ['name' => $this->bar->name, 'economy' => $economy->name])
            : __('mail.balanceImport.update.subtitle', ['economy' => $economy->name]);

        // Build the mail
        return parent::build()
            ->with('subtitle', $subtitle)
            ->with('user_name', $user_name)
            ->with('balance', $this->balance)
            ->with('balanceChange', $this->balanceChange)
            ->with('change', $this->change)
            ->with('event', $event)
            ->with('system', $system)
            ->with('community', $economy->community)
            ->with('economy', $economy)
            ->with('message', $this->message)
            ->with('invite_to_bar', $this->invite_to_bar)
            ->with('mutation', $this->mutation)
            ->with('wallet', $this->wallet)
            ->markdown(self::VIEW);
    }

    /**
     * Get the worker queue to put this mailable on.
     * @return string
     */
    protected function getWorkerQueue() {
        return self::QUEUE;
    }
}
