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
 * @property decimal balance
 * @property decimal cost
 * @property int currency_id
 * @property-read Currency currency
 * @property int|null submitter_id
 * @property-read User|null submitter
 * @property int|null accepter_id
 * @property-read User|null accepter
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

    /**
     * A scope to limit to changes only accepted or not accepted.
     */
    public function scopeAccepted($query, $accepted = true) {
        return $accepted ? $query->whereNotNull('accepted_at') : $query->whereNull('accepted_at');
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
     * Get a relation to the user that accepted this change, if it has been
     * accepted.
     *
     * @return Relation to reviewer.
     */
    public function accepter() {
        return $this->belongsTo(User::class, 'accepter_id');
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
            'color' => $this->accepted_at != null,
        ]);
    }
}
