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
 * Transaction model.
 *
 * This represents a transaction.
 *
 * @property int id
 * @property string|null description
 * @property int state
 * @property int|null reference_to
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Transaction extends Model {

    protected $table = "transactions";

    /**
     * Get the mutations that are part of this transaction.
     *
     * @return The mutations.
     */
    public function mutations() {
        return $this->hasMany('App\Models\Mutation');
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
     * Get the reference to another transaction, if set.
     *
     * @return The other transaction that is referred.
     */
    public function reference() {
        return $this->belongsTo('App\Models\Transaction');
    }

    // /**
    //  * Format the mutaion amount as human readable text using the proper
    //  * currency format.
    //  *
    //  * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
    //  *
    //  * @return string Formatted balance
    //  */
    // public function formatAmount($format = BALANCE_FORMAT_PLAIN) {
    //     return balance($this->amount, $this->currency->code, $format);
    // }
}
