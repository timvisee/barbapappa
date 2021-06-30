<?php

namespace App\Jobs;

use App\Mail\BalanceImport\Update;
use App\Models\BalanceImportAlias;
use App\Models\BalanceImportEvent;
use App\Models\BalanceImportSystem;
use App\Models\Bar;
use App\Utils\MoneyAmount;
use App\Utils\MoneyAmountBag;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Mail balance import system users a balance update.
 */
class BalanceImportSystemMailUpdate implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    private $system_id;
    private $alias_id;
    private $event_id;
    private $mail_unregistered_users;
    private $mail_not_joined_users;
    private $mail_joined_users;
    private $message;
    private $bar_id;
    private $invite_to_bar;
    private $default_locale;

    /**
     * Create a new job instance.
     *
     * @param int $system_id System ID.
     * @param int $alias_id Alias ID.
     * @param int|null $event_id Optional event ID to limit to.
     * @param bool $mail_unregistered_users Whether to mail unregistered users.
     * @param bool $mail_not_joined_users Whether to mail not-joined users.
     * @param bool $mail_joined_users Whether to mail joined users.
     * @param string|null $message Optional extra message.
     * @param int|null $bar_id Related bar ID.
     * @param bool $invite_to_bar Whether to invite user to the bar.
     * @param string|null $default_locale The default locale to use if user
     *      locale is unknown.
     */
    public function __construct(
        int $system_id,
        int $alias_id,
        ?int $event_id,
        bool $mail_unregistered_users,
        bool $mail_not_joined_users,
        bool $mail_joined_users,
        ?string $message,
        ?int $bar_id,
        bool $invite_to_bar,
        ?string $default_locale
    ) {
        // Set queue
        $this->onQueue(Self::QUEUE);

        $this->system_id = $system_id;
        $this->alias_id = $alias_id;
        $this->event_id = $event_id;
        $this->mail_unregistered_users = $mail_unregistered_users;
        $this->mail_not_joined_users = $mail_not_joined_users;
        $this->mail_joined_users = $mail_joined_users;
        $this->message = $message;
        $this->bar_id = $bar_id;
        $this->invite_to_bar = $invite_to_bar;
        $this->default_locale = $default_locale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Get system, alias, select bar
        $system = BalanceImportSystem::find($this->system_id);
        $alias = BalanceImportAlias::find($this->alias_id);
        if($system == null || $alias == null)
            return;
        $bar = Bar::find($this->bar_id);

        // Get system and event if available
        // TODO: remove event, we aren't using it?
        $event = BalanceImportEvent::find($this->event_id);

        // Attempt to find last approved balance import change for the user
        $last_change_query = $alias
            ->changes()
            ->approved()
            ->latest();
        // TODO: properly limit to given system!
        // if($system != null)
        //     $last_change_query = $last_change_query->where('system_id', $system->id);
        $last_change = $last_change_query->limit(1)->first();

        // Get user state for alias, do not invite to bar if already joined
        $user_state = $alias->getUserState();
        $request_to_verify = ($user_state != BalanceImportAlias::USER_STATE_UNREGISTERED) && $alias->hasUnverifiedEmail();
        $invite_to_bar = $bar != null && $this->invite_to_bar && $user_state != BalanceImportAlias::USER_STATE_JOINED;

        // User must meet filter requirements
        switch($user_state) {
            case BalanceImportAlias::USER_STATE_UNREGISTERED:
                if(!$this->mail_unregistered_users)
                    return;
                break;
            case BalanceImportAlias::USER_STATE_NOT_JOINED:
                if(!$this->mail_not_joined_users)
                    return;
                break;
            case BalanceImportAlias::USER_STATE_JOINED:
                if(!$this->mail_not_joined_users)
                    return;
                break;
            default:
                throw new \Exception("Unhandled user state");
        }

        // Get alias balances, we're done if zero
        $balances = $this->calculateAliasBalances($alias, $system);
        if($balances->isZero())
            return;

        // Get the email recipients, set default locale on them
        $recipients = $alias->toEmailRecipients();
        if(!empty($this->default_locale)) {
            $default_locale = $this->default_locale;
            $recipients = $recipients->map(function($recipient) use($default_locale) {
                $recipient->default_locale = $default_locale;
                return $recipient;
            });
        }

        // Create the mailable for the change, send the mailable
        Mail::send(new Update(
            $recipients,
            $alias,
            $event,
            $last_change,
            $this->message,
            $bar,
            $invite_to_bar,
            $balances,
            null, // TODO: impl change: $balanceChange,
            $request_to_verify,
        ));
    }

    /**
     * Calculate the balance for a given alias.
     *
     * TODO: move this into the alias model?
     *
     * @param ?BalanceImportSystem $system A system to limit to.
     *
     * @return MoneyAmountBag Money amounts.
     */
    function calculateAliasBalances(BalanceImportAlias $alias, ?BalanceImportSystem $system): MoneyAmountBag {
        // Collect the user balances
        $balances = new MoneyAmountBag();

        // Add balances for user wallets
        // These wallets are created automatically and linked to the alias when
        // bar members buy products on behalf of an imported user that didn't
        // create an account yet
        foreach($alias->economyMembers as $member)
            foreach($member->wallets as $wallet)
                $balances->add($wallet->getMoneyAmount());

        // Get all approved and uncommitted changes for the alias user
        // Committed changes are applied to wallets which are already counted
        // We group them by system for further processing by-system
        $changes = $alias
            ->changes()
            ->approved()
            ->committed(false)
            ->latest()
            ->with('event')
            ->get();
        $systemChanges = $changes->groupBy(function($change) {
            return $change->event->system_id;
        });

        // For each system, add uncommitted balance update/costs to balance
        foreach($systemChanges as $system_id => $changes) {
            // If a system is given, limit to it
            if($system != null && $system->id != $system_id)
                continue;

            // Can only count latest balance change for each currency, keep track of this
            $handledCurrencyBalance = collect();

            foreach($changes as $change) {
                // Only balance or cost must be set
                if($change->balance != null && $change->cost != null)
                    throw new \Exception("Cannot process balance import change, got both balance change and cost");
                if($change->balance == null && $change->cost == null)
                    throw new \Exception("Cannot process balance import change, got no balance change and no cost");

                // Handle balance or cost
                if($change->balance != null) {
                    if($handledCurrencyBalance->contains($change->currency_id))
                        continue;
                    $handledCurrencyBalance->push($change->currency_id);
                    $balances->add(new MoneyAmount($change->currency, $change->balance));
                } else {
                    $balances->sub(new MoneyAmount($change->currency, $change->cost));
                }
            }
        }

        // // Calculate/get balance change
        // $balanceChange = null;
        // if($mutation != null) {
        //     $balanceChange = new MoneyAmount($mutation->currency, $mutation->amount);
        // } else {
        //     // TODO: does this work for 'cost' imports?
        //     $previous = $change->previous()->first();
        //     if($previous != null) {
        //         $balanceChange = new MoneyAmount($change->currency, $change->balance - $previous->balance);
        //     }
        // }

        return $balances;
    }
}
