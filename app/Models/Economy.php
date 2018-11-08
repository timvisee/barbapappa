<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Utils\EmailRecipient;

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
     * Get the bars that use this economy.
     *
     * @return The bars.
     */
    public function bars() {
        return $this->hasMany('App\Models\Bar');
    }

    /**
     * Get a list of supported currencies within this economy.
     *
     * @return List of supported currencies.
     */
    public function supportedCurrencies() {
        return $this->hasMany('App\Models\CurrencySupport');
    }
}
