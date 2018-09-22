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
 * CurrencySupport model.
 *
 * This defines what currencies are supported in an economy.
 *
 * @property int id
 * @property int economy_id
 * @property bool enabled
 * @property int currency_id
 * @property bool allow_wallet
 * @property int product_price_default
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class CurrencySupport extends Model {

    /**
     * Get the economy this currency support model is part of.
     *
     * @return The economy.
     */
    public function economy() {
        return $this->belongsTo('App\Models\Economy');
    }
}
