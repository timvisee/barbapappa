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
 * Economy model.
 *
 * @property int id
 * @property int community_id
 * @property string name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Economy extends Model {

    /**
     * Get the community this economy is part of.
     *
     * @return The community.
     */
    public function community() {
        return $this->belongsTo('App\Models\Community');
    }

    /**
     * Get a list of bars that use this economy.
     *
     * @return The list of bars.
     */
    public function bars() {
        return $this->hasMany('App\Models\Bar');
    }

    /**
     * Get the list of supported currencies in this economy.
     * Note that some returned currencies might be disabled.
     *
     * @return The list of supported currencies.
     */
    public function supportedCurrencies() {
        return $this->hasMany('App\Models\CurrencySupport');
    }
}
