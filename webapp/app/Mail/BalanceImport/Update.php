<?php

namespace App\Mail\BalanceImport;

use App\Mail\PersonalizedEmail;
use App\Models\BalanceImportAlias;
use App\Models\BalanceImportEvent;
use App\Models\BalanceImportChange;
use App\Models\Bar;
use App\Utils\EmailRecipient;
use App\Utils\MoneyAmount;
use App\Utils\MoneyAmountBag;
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
     * Balance import alias.
     * @var BalanceImportAlias
     */
    private $alias;

    /**
     * Related event.
     * @bar ?BalanceImportEvent
     */
    private $event;

    /**
     * The last balance import change.
     * @var ?BalanceImportChange
     */
    private $last_change;

    /**
     * An optional extra message.
     * @var ?string
     */
    private $message;

    /**
     * The related bar.
     * @var ?Bar
     */
    private $bar;

    /**
     * Whether to invite the user to the bar.
     * @var bool
     */
    private $invite_to_bar;

    /**
     * The current balance.
     * @var MoneyAmountBag
     */
    private $balance;

    /**
     * Balance change.
     * @var MoneyAmount
     */
    private $balance_change;

    /**
     * Whether the user joined the application.
     * @var bool
     */
    private $joined;

    /**
     * Whether to request the user to verify their email address.
     * @var bool
     */
    private $request_to_verify;

    /**
     * Constructor.
     *
     * @param EmailRecipient[] $recipients A list of email recipients.
     * @param BalanceImportAlias $alias Balance import alias.
     * @param BalanceImportChange|null $last_change The last balance import change.
     * @param string|null $message An extra message.
     * @param Bar|null $bar Bar this import change is for.
     * @param bool $invite_to_bar True to invite user to bar.
     * @param MoneyAmountBag $balance The current balance.
     * @param MoneyAmount|null $balance_change The balance change.
     * @param bool $joined Whether the user has joined the platform.
     * @param bool $request_to_verify Whether to request the user to verify
     *          their email address.
     */
    public function __construct(
        $recipients,
        BalanceImportAlias $alias,
        ?BalanceImportEvent $event,
        ?BalanceImportChange $last_change,
        ?string $message,
        ?Bar $bar,
        bool $invite_to_bar,
        MoneyAmountBag $balance,
        ?MoneyAmount $balance_change,
        bool $joined,
        bool $request_to_verify
    ) {
        // Construct the parent
        parent::__construct($recipients, self::SUBJECT);

        $this->alias = $alias;
        $this->event = $event;
        $this->last_change = $last_change;
        $this->message = $message;
        $this->bar = $bar;
        $this->invite_to_bar = $invite_to_bar;
        $this->balance = $balance;
        $this->balance_change = $balance_change;
        $this->joined = $joined;
        $this->request_to_verify = $request_to_verify;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        // Get change/balance update details
        $alias = $this->alias;
        $economy = $alias->economy;
        $event = $this->event ?? ($this->last_change != null ? $this->last_change->event : null);
        $system = $event != null ? $event->system : null;
        $user = $alias->user()->first();
        $user_name = $user != null ? $user->first_name : $alias->name;

        // Build the mail
        $mail = parent::build();

        // Build dynamic subtitle
        $subtitle = $this->bar != null
            ? __('mail.balanceImport.update.subtitleWithBar', ['name' => $this->bar->name, 'economy' => $economy->name])
            : __('mail.balanceImport.update.subtitle', ['economy' => $economy->name]);

        // TODO: report all balances!
        $balance = $this
            ->balance
            ->amounts()
            ->sortByDesc(function($amount) {
                 return abs($amount->amount);
            })
            ->first();

        // Bind values to mail
        // TODO: provide specific wallet
        $mail->with('subtitle', $subtitle)
            ->with('user_name', $user_name)
            ->with('balance', $balance)
            ->with('balance_change', $this->balance_change)
            ->with('last_change', $this->last_change)
            ->with('event', $event)
            ->with('system', $system)
            ->with('community', $economy->community)
            ->with('economy', $economy)
            ->with('message', $this->message)
            ->with('bar', $this->bar)
            ->with('invite_to_bar', $this->invite_to_bar)
            ->with('joined', $this->joined)
            ->with('request_to_verify', $this->request_to_verify)
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
