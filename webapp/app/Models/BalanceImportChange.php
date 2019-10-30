<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
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
 * @property decimal cost
 * @property decimal balance
 * @property int currency_id
 * @property-read Currency currency
 * @property int|null creator_id
 * @property-read User|null creator
 * @property int|null reviewer_id
 * @property-read User|null reviewer
 * @property int|null mutation_id
 * @property-read Mutation|null mutation
 * @property Carbon reviewed_at
 * @property Carbon committed_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Wallet extends Model {

    protected $table = 'balance_import_change';

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
     * Get a relation to the user that created this change.
     *
     * @return Relation to creator.
     */
    public function creator() {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get a relation to the user that reviewed this change, if it has been
     * reviewed.
     *
     * @return Relation to reviewer.
     */
    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get a relation to the mutation for the committed change in the user
     * wallet, if this change has been committed.
     *
     * @return Relation to mutation.
     */
    public function mutation() {
        return $this->belongsTo(Mutation::class);
    }

    /**
     * Format the final balance at the time this change was snapshotted.
     * This is the user balance on the external system we imported from.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     *
     * @return string|null Formatted balance.
     */
    public function formatBalance($format = BALANCE_FORMAT_PLAIN) {
        if($this->balance == null)
            return null;
        return $this->currency->formatAmount($this->balance, $format);
    }

    /**
     * Format the cost of this change.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The cost formatting type.
     *
     * @return string Formatted cost.
     */
    public function formatCost($format = BALANCE_FORMAT_PLAIN) {
        return $this->currency->formatAmount($this->cost, $format);
    }
}
