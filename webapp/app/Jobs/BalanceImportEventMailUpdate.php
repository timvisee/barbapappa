<?php

namespace App\Jobs;

use App\Mail\BalanceImport\Update;
use App\Models\BalanceImportAlias;
use App\Models\BalanceImportChange;
use App\Models\Bar;
use App\Models\MutationWallet;
use App\Utils\MoneyAmount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Mail balance import event users a balance update.
 */
class BalanceImportEventMailUpdate implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    private $change_id;
    private $mail_unregistered_users;
    private $mail_not_joined_users;
    private $mail_joined_users;
    private $message;
    private $invite_to_bar_id;
    private $default_locale;

    /**
     * Create a new job instance.
     *
     * @param int $change_id Change ID.
     * @param bool $mail_unregistered_users Whether to mail unregistered users.
     * @param bool $mail_not_joined_users Whether to mail not-joined users.
     * @param bool $mail_joined_users Whether to mail joined users.
     * @param string|null $message Optional extra message.
     * @param int|null $invite_to_bar_id Bar ID to invite user to.
     * @param string|null $default_locale The default locale to use if user
     *      locale is unknown.
     */
    public function __construct(
        int $change_id,
        bool $mail_unregistered_users,
        bool $mail_not_joined_users,
        bool $mail_joined_users,
        $message,
        $invite_to_bar_id,
        $default_locale
    ) {
        // Set queue
        $this->onQueue(Self::QUEUE);

        $this->change_id = $change_id;
        $this->mail_unregistered_users = $mail_unregistered_users;
        $this->mail_not_joined_users = $mail_not_joined_users;
        $this->mail_joined_users = $mail_joined_users;
        $this->message = $message;
        $this->invite_to_bar_id = $invite_to_bar_id;
        $this->default_locale = $default_locale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // Load change and bar from database
        $change = BalanceImportChange::find($this->change_id);
        $bar = $invite_to_bar = Bar::find($this->invite_to_bar_id);
        if($change == null)
            return;

        // Get user state for alias, do not invite to bar if already joined
        $alias = $change->alias;
        $user_state = $alias->getUserState();
        $request_to_verify = ($user_state != BalanceImportAlias::USER_STATE_UNREGISTERED) && $alias->hasUnverifiedEmail();
        if($user_state == BalanceImportAlias::USER_STATE_JOINED)
            $invite_to_bar = null;

        // User must meet filter requirements
        if($user_state == BalanceImportAlias::USER_STATE_UNREGISTERED && !$this->mail_unregistered_users)
            return;
        if($user_state == BalanceImportAlias::USER_STATE_NOT_JOINED && !$this->mail_not_joined_users)
            return;
        if($user_state == BalanceImportAlias::USER_STATE_JOINED && !$this->mail_joined_users)
            return;

        // Get balance, skip if zero
        $balance = new MoneyAmount($change->currency, $change->balance);

        // Find mutation/wallet used for change if there is any, get balance
        $mutation = $change->mutation;
        $wallet = null;
        if($mutation != null) {
            $wallet_mutation = $mutation->dependOn;
            if($wallet_mutation != null) {
                $mutationable = $wallet_mutation->mutationable;
                if($mutationable instanceof MutationWallet)
                    $wallet = $mutationable->wallet;
            }
        }
        if($wallet != null)
            $balance = new MoneyAmount($wallet->currency, $wallet->balance);

        // Calculate/get balance change
        $balanceChange = null;
        if($mutation != null) {
            $balanceChange = new MoneyAmount($mutation->currency, $mutation->amount);
        } else {
            // TODO: does this work for 'cost' imports?
            $previous = $change->previous()->first();
            if($previous != null) {
                $balanceChange = new MoneyAmount($change->currency, $change->balance - $previous->balance);
            }
        }

        // If user has zero balance and has no change, ignore
        if($balance->amount == null && ($balanceChange != null && $balanceChange->amount == 0))
            return;

        // Get the email recipient
        $recipient = $alias->toEmailRecipient();
        if(!empty($this->default_locale))
            $recipient->default_locale = $this->default_locale;

        // Create the mailable for the change, send the mailable
        Mail::send(new Update(
            $recipient,
            $change,
            $this->message,
            $invite_to_bar,
            $bar,
            $mutation,
            $wallet,
            $balance,
            $balanceChange,
            $request_to_verify
        ));
    }
}
