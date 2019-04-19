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

    const STATE_PENDING = 1;
    const STATE_PROCESSING = 2;
    const STATE_SUCCESS = 3;
    const STATE_FAILED = 4;

    /**
     * Get the mutations that are part of this transaction.
     *
     * @return The mutations.
     */
    public function mutations() {
        return $this->hasMany('App\Models\Mutation');
    }

    /**
     * Get the reference to another transaction, if set.
     *
     * @return The other transaction that is referred.
     */
    public function reference() {
        return $this->belongsTo('App\Models\Transaction', 'reference_to');
    }

    /**
     * Determine the amount of money it costs the user to make this transaction.
     *
     * TODO: verify this statement is true
     * If the user pays money, the returned value is positive. If the user
     * receives/deposits money, the returned value is negative.
     *
     * @return The cost is returned as decimal value.
     */
    public function cost() {
        return $this
            ->mutations()
            ->where('type', Mutation::TYPE_WALLET)
            ->pluck('amount')
            ->sum();
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
     * @return A transaction description.
     */
    public function describe() {
        // Use the user description as base
        $text = $this->description;
        if(empty(trim($text)))
            $text = '?';

        // TODO: describe the transaction here, deposit, bought products?

        return $text;
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
     *
     * @return string Formatted cost.
     */
    public function formatCost($format = BALANCE_FORMAT_PLAIN, $invert = false) {
        // Determine the cost
        $cost = $this->cost();
        if($invert)
            $cost *= -1;

        // TODO: choose the correct currency here based on transactions
        // return balance($cost, $this->currency->code, $format);
        return balance($cost, 'EUR', $format);
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
            throw new \Exception("Unknown mutation state, cannot get state name");

        // Translate and return
        return __('pages.mutations.state.' . $key);
    }
}
