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
 * @property-read string code
 * @property-read string symbol
 * @property-read string format
 * @property-read string exchange_rate
 * @property-read boolean active
 * @property-read Carbon created_at
 * @property-read Carbon updated_at
 */
class Currency extends Model {

    /**
     * Get the currency support entries for bars, that use this currency.
     * Note that this might also return disabled currency support entries.
     *
     * @return The list of currency support entries.
     */
    public function supportedCurrencies() {
        return $this->hasMany('App\Models\CurrencySupport');
    }
}
