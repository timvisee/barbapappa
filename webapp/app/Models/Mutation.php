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
 * Mutation model.
 *
 * This represents a mutation of a transaction.
 *
 * @property int id
 * @property int transaction_id
 * @property int type
 * @property decimal amount
 * @property int currency_id
 * @property int state
 * @property int|null depend_on
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Mutation extends Model {

    protected $table = "mutations";

    /**
     * Get the transaction this mutation is part of.
     *
     * @return The transaction.
     */
    public function transaction() {
        return $this->belongsTo('App\Models\Transaction');
    }

    // /**
    //  * Get the economy this mutation is taking place in.
    //  *
    //  * @return The economy.
    //  */
    // public function economy() {
    //     return $this->belongsTo('App\Models\Economy');
    // }

    /**
     * Get the used currency.
     *
     * This is not the economy currency as specified in the current economy.
     * Rather it's a direct link to the currency used for this mutation.
     *
     * @return The currency.
     */
    public function currency() {
        return $this->belongsTo('App\Models\Currency');
    }

    /**
     * Get the mutation this depends on, if set.
     *
     * This mutation might depend on some other mutation before it can be marked
     * as complete. This returns the relation to the dependant, if there is any.
     *
     * @return The mutation this mutation depends on.
     */
    public function dependsOn() {
        // TODO: ensure this relation is configured correctly
        return $this->belongsTo('App\Models\Mutation', 'depends_on');
    }

    /**
     * Get all the mutations that depend on this mutation.
     *
     * Some mutation might depend on this mutation before they can be marked
     * as complete. This returns the relation to the depending, if there are
     * any.
     *
     * @return The mutations depending on this mutation.
     */
    public function depending() {
        return $this->hasMany('App\Models\Mutation', 'depends_on');
    }

    /**
     * Format the mutaion amount as human readable text using the proper
     * currency format.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     *
     * @return string Formatted balance
     */
    public function formatAmount($format = BALANCE_FORMAT_PLAIN) {
        return balance($this->amount, $this->currency->code, $format);
    }
}
