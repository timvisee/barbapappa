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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $change_id, bool $mail_unregistered_users, bool $mail_not_joined_users, bool $mail_joined_users, $message, $invite_to_bar_id) {
        // Set queue
        $this->onQueue(Self::QUEUE);

        $this->change_id = $change_id;
        $this->mail_unregistered_users = $mail_unregistered_users;
        $this->mail_not_joined_users = $mail_not_joined_users;
        $this->mail_joined_users = $mail_joined_users;
        $this->message = $message;
        $this->invite_to_bar_id = $invite_to_bar_id;
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
        $has_verified = $alias->hasVerifiedEmail();
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

        // Create the mailable for the change, send the mailable
        Mail::send(new Update(
            $alias->toEmailRecipient(),
            $change,
            $this->message,
            $invite_to_bar,
            $bar,
            $mutation,
            $wallet,
            $balance,
            $balanceChange,
            $has_verified
        ));
    }
}
