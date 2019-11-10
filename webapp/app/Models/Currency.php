<?php

namespace App\Models;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Utils\EmailRecipient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Currency model.
 *
 * @property-read int id
 * @property-read string name
 * @property-read string displayName
 * @property-read string code
 * @property-read string symbol
 * @property-read string format
 * @property-read string exchange_rate
 * @property-read boolean active
 * @property-read Carbon created_at
 * @property-read Carbon updated_at
 */
class Currency extends Model {

    protected $table = 'currency';

    /**
     * Get dynamic properties.
     *
     * @param string $name Property name.
     *
     * @return mixed|string Result.
     */
    public function __get($name) {
        switch($name) {
            case 'displayName':
                return $this->name . ': ' . $this->symbol;
            default:
                return parent::__get($name);
        }
    }

    /**
     * Check whether dynamic properties exist.
     *
     * @param string $name Property name.
     *
     * @return bool True if exists, false if not.
     */
    public function __isset($name) {
        switch($name) {
            case 'displayName':
                return true;
            default:
                return parent::__isset($name);
        }
    }

    /**
     * Get the economy currency entries for bars, that use this currency.
     * Note that this might also return disabled currency support entries.
     *
     * @return The list of economy currency entries.
     */
    public function economyCurrencies() {
        return $this->hasMany('App\Models\EconomyCurrency');
    }

    /**
     * Get the wallets created by users that use this currency.
     *
     * @return The wallets.
     */
    public function wallets() {
        return $this->hasMany('App\Models\Wallet');
    }

    /**
     * Format the given amount as human readable text using the proper currency
     * format.
     *
     * @param decimal $amount The amount to format.
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     * @param array [$options=[]] Formatting options.
     *
     * @return string Formatted amount.
     */
    public function formatAmount($amount, $format = BALANCE_FORMAT_PLAIN, $options = []) {
        return balance($amount, $this->code, $format, $options);
    }
}
