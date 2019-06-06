<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Transaction model.
 *
 * This represents a transaction.
 *
 * @property int id
 * @property string|null description
 * @property int state
 * @property int|null reference_to
 * @property int|null owner_id
 * @property-read User|null owner
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Transaction extends Model {

    protected $table = "transactions";

    protected $with = ['mutations'];

    protected $fillable = ['state', 'owner_id'];

    const STATE_PENDING = 1;
    const STATE_PROCESSING = 2;
    const STATE_SUCCESS = 3;
    const STATE_FAILED = 4;

    const SETTLED = [
        Self::STATE_SUCCESS,
        Self::STATE_FAILED,
    ];

    /**
     * The maximum allowed lifetime in seconds of a transaction that still
     * allows undoing.
     */
    const UNDO_MAX_LIFETIME = 15 * 60;

    /**
     * Get the mutations that are part of this transaction.
     * Ordered by their amount, positive (incomming) first, negative (outgoing) last.
     *
     * @return The mutations.
     */
    public function mutations() {
        return $this->hasMany(Mutation::class)->orderBy('amount', 'DESC');
    }

    /**
     * Get the reference to another transaction, if set.
     *
     * @return The other transaction that is referred.
     */
    public function referencedTo() {
        return $this->belongsTo(Self::class, 'reference_to');
    }

    /**
     * Get a relation to all transactions that reference this one.
     *
     * @return Relation to other transactions that refer this one.
     */
    public function referencedBy() {
        return $this->hasMany(Self::class, 'reference_to');
    }

    /**
     * Get a relation to the user that owns this transaction.
     * This is usually the user that initiated this transaction.
     *
     * @return Relation to the user that created this transaction.
     */
    public function owner() {
        return $this->belongsTo('App\Models\User', 'owner_id');
    }

    /**
     * Determine the amount of money it costs the user to make this transaction.
     *
     * If the user pays money, the returned value is negative. If the user
     * receives/deposits money, the returned value is positive.
     *
     * The cost is based on wallet mutations. If no wallet mutations are
     * avaialble, payment mutations are considered instead. If none are found,
     * 0 is returned.
     *
     * @param Wallet|null [$perspective=null] An optional perspective we're
     *      looking from. Provide the wallet if seeing this transaction for a
     *      wallet.
     *
     * @return The cost is returned as decimal value.
     */
    // TODO: rename this to gain?
    public function cost($perspective = null) {
        // Determine cost based on wallet
        $query = $this
            ->mutations()
            ->where('type', Mutation::TYPE_WALLET);
        if($perspective instanceof Wallet) {
            $query = $query
                ->whereExists(function($query) use($perspective) {
                    $query->selectRaw('1')
                        ->from('mutations_wallet')
                        ->whereRaw('mutations.id = mutations_wallet.mutation_id')
                        ->where('wallet_id', $perspective->id);
                });
        }
        if(($cost = -$query->pluck('amount')->sum()) != 0)
            return $cost;

        // Determine cost based on payments
        $cost = -$this
            ->mutations()
            ->where('type', Mutation::TYPE_PAYMENT)
            ->pluck('amount')
            ->sum();
        if($cost != 0)
            return $cost;

        // Find total transaction value based on wallets
        list($pos, $neg) = $this
            ->mutations()
            ->where('type', Mutation::TYPE_WALLET)
            ->pluck('amount')
            ->partition(function($amount) {
                return $amount >= 0;
            });
        $cost = max($pos->sum(), abs($neg->sum()));
        if($cost != 0)
            return $cost;

        // Find total transaction value based on payments
        list($pos, $neg) = $this
            ->mutations()
            ->where('type', Mutation::TYPE_PRODUCT)
            ->pluck('amount')
            ->partition(function($amount) {
                return $amount >= 0;
            });
        $cost = max($pos->sum(), abs($neg->sum()));
        if($cost != 0)
            return $cost;

        // TODO: throw warning no cost was found based on wallet/payment mutations

        // No cost could be determined
        return 0;
    }

    /**
     * Describe the transaction.
     * This description may be printed in a transaction overview or list.
     *
     * If the `description` field on the transaction is set, it will be
     * returned being a custom description.
     *
     * Otherwise a description is generated based on the mutations, for example:
     * - Wallet deposit
     * - Purchased 5 products
     *
     * @param bool [$details=false] True to return a longer, more detailed,
     *      description. Recommended to use on detail pages.
     *
     * @return A transaction description.
     */
    public function describe($details = false) {
        // Use the user description as base
        $text = $this->description;
        if(!empty(trim($text)))
            return $text;

        // Determine whether to use a suffix
        $suffix = $details ? ' (' . strtolower(__('misc.estimate')) . ')' : '';

        // Collect all mutation types, separate by deposit/withdraw
        // TODO: create method for this, also used somewhere else! (search partition)
        list($to, $from) = $this
            ->mutations
            ->map(function($m) {
                return [$m->type, $m->amount];
            })->partition(function($m) {
                return $m[1] < 0;
            });
        $to = $to->pluck(0);
        $from = $from->pluck(0);

        // Based on the mutation types, find a fitting description
        if($from->containsStrict(Mutation::TYPE_WALLET) && $to->containsStrict(Mutation::TYPE_PRODUCT))
            return __('pages.transactions.descriptions.fromWalletToProduct') . $suffix;
        else if($to->containsStrict(Mutation::TYPE_PRODUCT))
            return __('pages.transactions.descriptions.toProduct') . $suffix;
        else if($from->containsStrict(Mutation::TYPE_PAYMENT && $to->containsStrict(Mutation::TYPE_WALLET)))
            return __('pages.transactions.descriptions.fromPaymentToWallet') . $suffix;
        else if($from->containsStrict(Mutation::TYPE_WALLET) && $to->containsStrict(Mutation::TYPE_WALLET))
            return __('pages.transactions.descriptions.fromWalletToWallet') . $suffix;
        else if($to->containsStrict(Mutation::TYPE_WALLET))
            return __('pages.transactions.descriptions.toWallet'). $suffix;
        else if($from->containsStrict(Mutation::TYPE_WALLET))
            return __('pages.transactions.descriptions.fromWallet') . $suffix;

        // Formulate description based on mutation descriptions
        $text = $this->mutations->map(function($m) {
            return $m->describe();
        })->implode(', ');
        return ucfirst(strtolower($text)) . $suffix;
    }

    /**
     * Format the amount of money it costs the user to make this transaction as
     * human readable text using the proper currency format.
     *
     * If the user pays money, the returned value is positive. If the user
     * receives/deposits money, the returned value is negative.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     * @param boolean [$invert=false] True to invert the cost value.
     * @param Wallet|null [$perspective=null] An optional perspective we're
     *      looking from. Provide the wallet if seeing this transaction for a
     *      wallet.
     *
     * @return string Formatted cost.
     */
    public function formatCost($format = BALANCE_FORMAT_PLAIN, $invert = false, $perspective = null) {
        // Determine the cost
        $cost = $this->cost($perspective);
        if($invert)
            $cost *= -1;

        // Blue in progress, green/red succeeded, gray failed
        $options = [];
        if($this->state == Self::STATE_FAILED)
            $options['color'] = false;
        else if($this->state != Self::STATE_SUCCESS)
            $options['neutral'] = true;

        // TODO: choose the correct currency here based on transactions
        // return balance($cost, $this->currency->code, $format);
        return balance($cost, 'EUR', $format, $options);
    }

    /**
     * Get the display name for the current transaction state.
     *
     * @return State display name.
     */
    public function stateName() {
        // Get the state key here
        $key = [
            Self::STATE_PENDING => 'pending',
            Self::STATE_PROCESSING => 'processing',
            Self::STATE_SUCCESS => 'success',
            Self::STATE_FAILED => 'failed',
        ][$this->state];
        if(empty($key))
            throw new \Exception("Unknown transaction state, cannot get state name");

        // Translate and return
        return __('pages.mutations.state.' . $key);
    }

    /**
     * Set the state of this transaction with some bound checks.
     *
     * @param int $state The state to set to.
     * @param boolean [$save=true] True to save the model after setting the state.
     *
     * @throws \Exception Throws if an invalid state is given.
     */
    private function setState($state, $save = true) {
        // Never allow setting to pending
        if($state == Self::STATE_PENDING)
            throw new \Exception('Cannot set transaction state to pending');

        // Set the state, and save
        $this->state = $state;
        if($save)
            $this->save();
    }

    /**
     * Settle this transaction.
     * This will fail if some mutations don't have a settled state yet.
     *
     * @param int $state The new transaction state to settle with.
     * @param bool [$save=true] True to save this model after settling.
     *
     * @throws \Exception Throws if an invalid settle state is given.
     */
    public function settle(int $state, $save = true) {
        // The given state must be in the settled array
        if(!in_array($state, Self::SETTLED))
            throw new \Exception('Failed to settle transaction, given new state is not recognized as settle state');

        // Skip if already in this state
        if($this->state == $state)
            return;

        // All mutations must be settled
        $allSettled = $this->mutations->every(function($m) {
            return $m->isSettled();
        });
        if(!$allSettled)
            throw new \Exception('Failed to settle transaction, not all it\'s mutations are settled yet');

        // TODO: must be in a transaction
        // TODO: make sure transaction state is still consistent!

        // Set the state, save the model
        $this->setState($state, $save);
    }

    /**
     * Undo the transaction.
     * This deletes the model on success.
     *
     * A database transaction must be active.
     *
     * @throws \Exception Throws if we cannot undo right now or if not in a
     *      database transaction.
     */
    public function undo() {
        // Assert we have an active database transaction
        if(DB::transactionLevel() <= 0)
            throw new \Exception("Transaction can only be undone when database transaction is active");

        // Assert we can undo
        if(!$this->canUndo())
            throw new \Exception("Attempting to undo transaction while this is not allowed");

        // Undo all mutations without deleting them
        $this->mutations->each(function($m) {
            $m->undo(false);
        });

        // Delete this transaction
        $this->delete();
    }

    /**
     * This method checks whether a user can undo this transaction.
     * This depends on the transaction lifetime, and contained mutations types.
     *
     * This check is expensive.
     *
     * @return bool True if it can be undone, false if not.
     */
    public function canUndo() {
        // TODO: has permission?

        // All mutations must be undoable
        $canUndo = $this->mutations->every(function($m) {
            return $m->canUndo();
        });
        if(!$canUndo)
            return false;

        // Assert the max lifetime for undoing, return result
        return !$this
            ->created_at
            ->copy()
            ->addSeconds(Self::UNDO_MAX_LIFETIME)
            ->isPast();
    }
}
