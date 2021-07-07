<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Perms\CommunityRoles;

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
 * @property int|null initiated_by_id
 * @property bool initiated_by_other
 * @property bool initiated_by_kiosk
 * @property-read User|null owner
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Transaction extends Model {

    protected $table = 'transaction';

    protected $with = ['mutations'];

    protected $fillable = ['state', 'description', 'owner_id', 'initiated_by_id', 'initiated_by_other', 'initiated_by_kiosk'];

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
     * Ordered by their amount, positive (incoming) first, negative (outgoing) last.
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
     * @return Relation to the user that owns this transaction.
     */
    public function owner() {
        return $this->belongsTo('App\Models\User', 'owner_id');
    }

    /**
     * Get a relation to the user that initiated this transaction.
     * This is usually the user that initiated this transaction.
     *
     * @return Relation to the user that initiated this transaction.
     */
    // TODO: rename this to initiatedByUser?
    public function initiatedBy() {
        return $this->belongsTo('App\Models\User', 'initiated_by_id');
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
            ->where('mutationable_type', MutationWallet::class);
        if($perspective instanceof Wallet) {
            $query = $query
                ->whereExists(function($query) use($perspective) {
                    $query->selectRaw('1')
                        ->from('mutation_wallet')
                        ->whereRaw('mutation.mutationable_id = mutation_wallet.id')
                        ->where('wallet_id', $perspective->id);
                });
        }
        if(($cost = -$query->pluck('amount')->sum()) != 0)
            return $cost;

        // Determine cost based on payments
        $cost = -$this
            ->mutations()
            ->where('mutationable_type', MutationPayment::class)
            ->pluck('amount')
            ->sum();
        if($cost != 0)
            return $cost;

        // Find total transaction value based on wallets
        list($pos, $neg) = $this
            ->mutations()
            ->where('mutationable_type', MutationWallet::class)
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
            ->where('mutationable_type', MutationProduct::class)
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
        // $suffix = $details ? ' (' . strtolower(__('misc.estimate')) . ')' : '';
        $suffix = '';

        // Collect all mutation types, separate by deposit/withdraw
        // TODO: create method for this, also used somewhere else! (search partition)
        list($to, $from) = $this
            ->mutations
            ->map(function($m) {
                return [$m->mutationable_type, $m->amount];
            })->partition(function($m) {
                return $m[1] < 0;
            });
        $to = $to->pluck(0);
        $from = $from->pluck(0);

        // Based on the mutation types, find a fitting description
        if($from->containsStrict(MutationBalanceImport::class) || $to->containsStrict(MutationBalanceImport::class))
            return __('pages.transactions.descriptions.balanceImport') . $suffix;
        else if($from->containsStrict(MutationWallet::class) && $to->containsStrict(MutationProduct::class))
            return __('pages.transactions.descriptions.fromWalletToProduct') . $suffix;
        else if($to->containsStrict(MutationProduct::class))
            return __('pages.transactions.descriptions.toProduct') . $suffix;
        else if($from->containsStrict(MutationPayment::class) && $to->containsStrict(MutationWallet::class))
            return __('pages.transactions.descriptions.fromPaymentToWallet') . $suffix;
        else if($from->containsStrict(MutationWallet::class) && $to->containsStrict(MutationWallet::class))
            return __('pages.transactions.descriptions.fromWalletToWallet') . $suffix;
        else if($to->containsStrict(MutationWallet::class))
            return __('pages.transactions.descriptions.toWallet'). $suffix;
        else if($from->containsStrict(MutationWallet::class))
            return __('pages.transactions.descriptions.fromWallet') . $suffix;

        // Formulate description based on mutation descriptions
        $text = $this->mutations->map(function($m) use($details) {
            return $m->describe($details);
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

        // Orange in progress, green/red succeeded, gray failed
        $options = [];
        switch($this->state) {
            case Self::STATE_FAILED:
            default:
                $options['color'] = false;
                break;
            case Self::STATE_PENDING:
            case Self::STATE_PROCESSING:
                $options['label-color'] = 'orange';
                $options['neutral'] = true;
                break;
            case Self::STATE_SUCCESS:
                break;
        }

        // TODO: choose the correct currency here based on transactions
        return $this->mutations->first()->currency->format($cost, $format, $options);
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

        // We must be in a database transaction
        assert_transaction();

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
     * @param bool [$force=false] True to attempt to force.
     *
     * @throws \Exception Throws if we cannot undo right now or if not in a
     *      database transaction.
     */
    public function undo($force = false) {
        // We must be in a database transaction
        assert_transaction();

        // Assert we can undo
        if(!$this->canUndo($force))
            throw new \Exception("Attempting to undo transaction while this is not allowed");

        // Undo all mutations, and delete them
        $this->mutations->each(function($m) use($force) {
            $m->undo(true, $force);
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
     * @param bool [$force=false] True to check if we can udno when forcing.
     *
     * @return bool True if it can be undone, false if not.
     */
    public function canUndo($force = false) {
        // User must have manage permissions
        if(!$this->hasManagePermission())
            return false;

        // All mutations must be undoable
        $canUndo = $this->mutations->every(function($m) use($force) {
            return $m->canUndo($force);
        });
        if(!$canUndo)
            return false;

        // Assert the max lifetime for undoing, return result
        return $force || !$this
            ->created_at
            ->copy()
            ->addSeconds(Self::UNDO_MAX_LIFETIME)
            ->isPast();
    }

    /**
     * Find a list of communities this transaction took part in.
     *
     * For example, if the transaction has a wallet mutation, the community the
     * wallet is in will be part of the returned list.
     *
     * @return Collection List of communities, may be empty.
     */
    public function findCommunities() {
        return $this
            ->mutations
            ->flatMap(function($mutation) {
                return $mutation->findCommunities();
            })
            ->filter(function($mutation) {
                return $mutation != null;
            })
            ->unique('id');
    }

    /**
     * Get a list of all relevant and related objects to this mutation.
     * Can be used to generate a list of links on a mutation inspection page, to
     * the respective objects.
     *
     * A transaction with a product and wallet mutation, would return product
     * and wallet objects.
     *
     * This is an expensive function.
     *
     * @return Collection List of objects.
     */
    public function getRelatedObjects() {
        return $this
            ->mutations
            ->flatMap(function($mutation) {
                return $mutation->getRelatedObjects();
            })
            ->unique();
    }

    /**
     * Check whether the currently authenticated user has permission to view this
     * transaction.
     *
     * @return boolean True if the user can view this transaction, false if not.
     */
    public function hasViewPermission() {
        return $this->hasPermission(false);
    }

    /**
     * Check whether the currently authenticated user has permission to manage
     * this transaction.
     *
     * @return boolean True if the user can manage this transaction, false if not.
     */
    public function hasManagePermission() {
        return $this->hasPermission(true);
    }

    /**
     * Check whether the currently authenticated user has permission to view this
     * transaction.
     *
     * Note: this is expensive.
     *
     * @param bool [$manage=true] True to check for management permissions,
     *      false for just viewing permission.
     * @return boolean True if the user can view this transaction, false if not.
     */
    private function hasPermission($manage = true) {
        // The user must be authenticated
        $barauth = barauth();
        if(!$barauth->isAuth())
            return false;
        $user = $barauth->getUser();

        // User is fine if he owns the transaction
        if($this->owner_id == $user->id)
            return true;

        // User is fine if it owns any mutation
        $ownsMutation = $this
            ->mutations
            ->filter(function($mutation) use($user) {
                return $mutation->owner_id == $user->id;
            })
            ->take(1)
            ->count() > 0;
        if($ownsMutation)
            return true;

        // User is fine if it owns any of the wallets
        $isWalletOwner = $this
            ->mutations()
            ->where('mutationable_type', MutationWallet::class)
            ->join('mutation_wallet', 'mutation.mutationable_id', 'mutation_wallet.id')
            ->join('wallet', 'mutation_wallet.wallet_id', 'wallet.id')
            ->join('economy_member', 'wallet.economy_member_id', 'economy_member.id')
            ->where('economy_member.user_id', $user->id)
            ->limit(1)
            ->count() > 0;
        if($isWalletOwner)
            return true;

        // Find all communities this transaction is part of, fine if manager in any
        $communities = $this->findCommunities();
        foreach($communities as $community)
            if(app('perms')->evaluate(CommunityRoles::presetManager(), $community, null))
                return true;

        return false;
    }
}
