<?php

namespace App\Models;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Scopes\EnabledScope;
use App\Utils\EmailRecipient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// TODO: update parent mutation change time, if this model changes

/**
 * Mutation wallet model.
 * This defines additional information for a wallet mutation, that belongs to a
 * main mutation.
 *
 * @property int id
 * @property int mutation_id
 * @property int|null wallet_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class MutationWallet extends Model {

    protected $table = "mutations_wallet";

    protected $with = ['wallet'];

    protected $fillable = [
        'mutation_id',
        'wallet_id',
    ];

    /**
     * Get the main mutation this wallet mutation data belongs to.
     *
     * @return The main mutation.
     */
    public function mutation() {
        return $this->belongsTo(Mutation::class);
    }

    /**
     * Get the wallet this mutation had an effect on.
     *
     * @return The affected wallet.
     */
    public function wallet() {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Undo the product mutation.
     * This does not delete the mutation model.
     *
     * A database transaction must be active.
     *
     * @throws \Exception Throws if we cannot undo right now or if not in a
     *      transaction.
     */
    public function undo() {
        // Assert we have an active database transaction
        if(DB::transactionLevel() <= 0)
            throw new \Exception("Mutation can only be undone when database transaction is active");

        // Determine whether we need to deposit or withdraw from the wallet
        $amount = $this->mutation->amount;
        $deposit = $amount >= 0;
        $amount = abs($amount);

        // Deposit/withdraw from the wallet balance
        if($deposit)
            $this->wallet->deposit($amount);
        else
            $this->wallet->withdraw($amount);
    }

    /**
     * Handle changes as effect of a state change.
     * This method is called when the state of the mutation changes.
     *
     * For a wallet mutation, this method would change the wallet balance when
     * the new state defines success.
     *
     * @param Mutation $mutation The mutation, parent of this instance.
     * @param int $oldState The old state.
     * @param int $newState The new, current state.
     */
    public function applyState(Mutation $mutation, int $oldState, int $newState) {
        // Make sure the currency IDs match
        if($this->wallet->currency_id != $mutation->currency_id)
            throw new \Exception('Wallet mutation and wallet differ in currency, cannot process');

        // Modify the wallet balance
        if($newState == Mutation::STATE_SUCCESS) {
            if($mutation->amount > 0)
                $this->wallet->withdraw($mutation->amount);
            else
                $this->wallet->deposit(-$mutation->amount);
        } else if($oldState == Mutation::STATE_SUCCESS) {
            if($mutation->amount > 0)
                $this->wallet->deposit($mutation->amount);
            else
                $this->wallet->withdraw(-$mutation->amount);
        }
    }
}
