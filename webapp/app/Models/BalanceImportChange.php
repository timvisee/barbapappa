<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Scopes\EnabledScope;
use App\Utils\EmailRecipient;

/**
 * Balance import change model.
 *
 * This represents an imported balance change for a single user.
 *
 * @property int id
 * @property int event_id
 * @property-read BalanceImportEvent event
 * @property int alias_id
 * @property-read BalanceImportAlias alias
 * @property decimal balance
 * @property decimal cost
 * @property int currency_id
 * @property-read Currency currency
 * @property int|null submitter_id
 * @property-read User|null submitter
 * @property int|null approver_id
 * @property-read User|null approver
 * @property int|null mutation_id
 * @property-read Mutation|null mutation
 * @property Carbon reviewed_at
 * @property Carbon committed_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class BalanceImportChange extends Model {

    protected $table = 'balance_import_change';

    protected $with = ['alias'];

    protected $fillable = [
        'alias_id',
        'cost',
        'balance',
        'currency_id',
        'submitter_id',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'committed_at' => 'datetime',
    ];

    public static function boot() {
        parent::boot();

        // Cascade delete to import alias if it's not linked to other changes
        static::deleted(function($model) {
            $alias = $model->alias;
            $hasOtherChanges = $alias->changes()->limit(1)->count() > 0;
            if(!$hasOtherChanges)
                $alias->delete();
        });
    }

    /**
     * A scope to limit to changes only approved or not approved.
     */
    public function scopeApproved($query, $approved = true) {
        return $approved ? $query->whereNotNull('approved_at') : $query->whereNull('approved_at');
    }

    /**
     * Get a relation to the balance import event.
     *
     * @return Relation to the balance import event.
     */
    public function event() {
        return $this->belongsTo(BalanceImportEvent::class, 'event_id');
    }

    /**
     * Get a relation to the balance import alias this change is for.
     *
     * @return Relation to the balance import alias.
     */
    public function alias() {
        return $this->belongsTo(BalanceImportAlias::class);
    }

    /**
     * Get the used currency.
     *
     * This is not the economy currency as specified in the economy this change
     * is related to.
     *
     * @return Currency The currency.
     */
    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get a relation to the user that submitted this change.
     *
     * @return Relation to submitter.
     */
    public function submitter() {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    /**
     * Get a relation to the user that approved this change, if it has been
     * approved.
     *
     * @return Relation to reviewer.
     */
    public function approver() {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Get the relation to the commit mutation if there is any.
     *
     * @return Relation to the mutation.
     */
    public function mutation() {
        return $this->belongsTo(Mutation::class);
    }

    /**
     * Check whether this change has been approved.
     *
     * @return bool True if approved, false if not.
     */
    public function isApproved() {
        return $this->approved_at != null && !$this->approved_at->isFuture();
    }

    /**
     * Approve this change.
     *
     * @throws \Exception Throws if the change had been approved already.
     */
    public function approve() {
        $user = barauth()->getUser();

        // Make sure it isn't approved yet
        if($this->isApproved())
            throw new \Exception("Attempting to approve balance import change that had been approved already");

        // Require previous change to be approved
        if($this->balance != null) {
            $previous = $this->previous()->first();
            if($previous != null && !$previous->isApproved())
                throw new \Exception("Cannot approve balance import change until previous change is approved");
        }

        $change = $this;
        DB::transaction(function() use($change, $user) {
            // Approve the change
            $change->approver_id = $user->id;
            $change->approved_at = now();
            $change->save();

            // TODO: remove after testing!
            $change->commit();
        });
    }

    /**
     * Undo this change.
     *
     * @throws \Exception Throws if the change had been approved already.
     */
    public function undo() {
        // Do nothing if not approved and not committed
        if(!$this->isApproved() && $this->mutation_id == null)
            return;

        // Require no newer approved changes to exist
        if($this->balance != null) {
            $followingApproved = $this->following()->whereNotNull('approved_at')->limit(1)->count() > 0;
            if($followingApproved)
                throw new \Exception("Cannot undo balance import change until next change is non-approved");
        }

        $change = $this;
        DB::transaction(function() use($change) {
            // Undo any wallet mutation
            if($change->mutation_id != null)
                $change->mutation->transaction->undo();

            // Update approval status
            $change->approver_id = null;
            $change->approved_at = null;
            $change->committed_at = null;
            $change->mutation_id = null;
            $change->save();
        });
    }

    /**
     * Check whether this change has been committed.
     *
     * @return bool True if committed, false if not.
     */
    public function isCommitted() {
        return $this->committed_at != null;
    }

    /**
     * Commit this change.
     *
     * @throws \Exception Throws if already committed, or if not approved.
     *
     * TODO: user must be known
     */
    public function commit() {
        // Make sure it isn't approved yet
        if(!$this->isApproved() || $this->isCommitted())
            throw new \Exception("Attempting to commit balance import change that has not been approved yet, or that has been committed already");

        // Calculate amount to deposit to the user wallet
        $amount = null;
        if($this->cost != null)
            $amount = -$this->cost;
        else {
            $previous = $this->previous()->first();
            if($previous != null && !$previous->isCommitted())
                throw new \Exception("Attempting to commit balance import change, while previous change is not committed");
            $amount = $this->balance - ($previous != null ? $previous->balance : 0);
        }

        $economy = $this->event->system->economy;
        $currency = $this->currency;
        $economyCurrency = $economy->currencies()->where('currency_id', $currency->id)->firstOrFail();
        $economyMember = $this->alias->economyMember($economy)->firstOrFail();
        $wallet = $economyMember->getOrCreateWallet(collect([$economyCurrency]));
        $user = $economyMember->user;

        $change = $this;
        DB::transaction(function() use(&$change, $user, $amount, $economy, $currency, $wallet) {
            // Create the transaction
            $transaction = Transaction::create([
                'state' => Transaction::STATE_SUCCESS,
                'owner_id' => $user->id,
            ]);

            // Create wallet mutation
            $mut_wallet = $transaction
                ->mutations()
                ->create([
                    'economy_id' => $economy->id,
                    'mutationable_id' => 0,
                    'mutationable_type' => '',
                    'amount' => -$amount,
                    'currency_id' => $currency->id,
                    'state' => Mutation::STATE_SUCCESS,
                    'owner_id' => $user->id,
                ]);
            $mut_wallet->setMutationable(
                MutationWallet::create([
                    'wallet_id' => $wallet->id,
                ])
            );

            // Create change mutation
            $mut_change = $transaction
                ->mutations()
                ->create([
                    'economy_id' => $economy->id,
                    'mutationable_id' => 0,
                    'mutationable_type' => '',
                    'amount' => $amount,
                    'currency_id' => $currency->id,
                    'state' => Mutation::STATE_SUCCESS,
                    'owner_id' => $user->id,
                    'depend_on' => $mut_wallet->id,
                ]);
            $mut_change->setMutationable(MutationMagic::create());

            // Update the wallet balance
            // TODO: do this by setting the mutation states instead
            if($amount > 0)
                $wallet->deposit($amount);
            else
                $wallet->withdraw(-$amount);

            // Update the change
            $change->committed_at = now();
            $change->mutation_id = $mut_change->id;
            $change->save();
        });
    }

    /**
     * Get a relation to previous balance changes for this alias in this system.
     * This only returns balance update changes, not cost changes.
     *
     * This is ordered from most recent to oldest.
     * Use `->first()` to obtain just the previous change.
     *
     * @return Relation to previous changes.
     */
    public function previous() {
        return $this
            ->event
            ->system
            ->changes()
            ->where('alias_id', $this->alias_id)
            ->where('balance', '!=', null)
            ->where('balance_import_change.created_at', '<', $this->created_at)
            ->latest('balance_import_change.created_at');
    }

    /**
     * Get a relation to following balance changes for this alias in this system.
     * This only returns balance update changes, not cost changes.
     *
     * This is ordered from most oldest to most recent.
     * Use `->first()` to obtain just the next change.
     *
     * @return Relation to previous changes.
     */
    public function following() {
        return $this
            ->event
            ->system
            ->changes()
            ->where('alias_id', $this->alias_id)
            ->where('balance', '!=', null)
            ->where('balance_import_change.created_at', '>', $this->created_at)
            ->oldest('balance_import_change.created_at');
    }

    /**
     * Format the balance for this change, returns null if there is no cost.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     *
     * @return string Formatted balance
     */
    public function formatBalance($format = BALANCE_FORMAT_PLAIN) {
        if($this->balance == null)
            return null;
        return $this->currency->formatAmount($this->balance, $format, ['neutral' => true]);
    }

    /**
     * Format the cost for this change, returns null if there is no cost.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     *
     * @return string Formatted cost
     */
    public function formatCost($format = BALANCE_FORMAT_PLAIN) {
        if($this->cost == null)
            return null;
        return $this->currency->formatAmount($this->cost, $format, ['neutral' => true]);
    }

    /**
     * Format the amount for this change, returns zero if there's no amount.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     *
     * @return string Formatted amount
     */
    public function formatAmount($format = BALANCE_FORMAT_PLAIN) {
        return $this->currency->formatAmount($this->balance ?? -$this->cost ?? 0, $format, [
            'neutral' => $this->balance != null,
            'color' => $this->approved_at != null,
        ]);
    }
}
