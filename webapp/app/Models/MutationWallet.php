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
 * @property int|null wallet_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class MutationWallet extends Model {

    use Mutationable;

    protected $table = 'mutation_wallet';

    protected $with = ['wallet'];

    protected $fillable = [
        'wallet_id',
    ];

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
        // Nothing to undo if amount was not applied to wallet
        if(!$this->isApplied())
            return;

        // We must be in a database transaction
        assert_transaction();

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

    /**
     * Check whether the amount of this wallet mutation is applied to the
     * wallet.
     *
     * @return bool True if applied, false if not.
     */
    public function isApplied() {
        return $this->mutation->state == Mutation::STATE_SUCCESS;
    }

    /**
     * Find a list of communities this transaction took part in.
     *
     * This will return the community the wallet is in if known.
     *
     * @return Collection List of communities, may be empty.
     */
    public function findCommunities() {
        if($this->wallet != null)
            return collect([$this->wallet->economyMember->economy->community]);
        return collect();
    }

    /**
     * Get a list of all relevant and related objects to this mutation.
     * Can be used to generate a list of links on a mutation inspection page, to
     * the respective objects.
     *
     * This will return the related wallet.
     *
     * This is an expensive function.
     *
     * @return Collection List of objects.
     */
    public function getRelatedObjects() {
        if($this->wallet_id != null)
            return [$this->wallet];
        return [];
    }
}
