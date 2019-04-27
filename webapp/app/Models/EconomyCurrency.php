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
 * EconomyCurrency model.
 *
 * This defines what currencies are supported in an economy.
 *
 * @property int id
 * @property int economy_id
 * @property int currency_id
 * @property-read string displayName
 * @property-read string name
 * @property-read string symbol
 * @property bool enabled
 * @property bool allow_wallet
 * @property int product_price_default
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class EconomyCurrency extends Model {

    protected $table = "economy_currencies";

    protected $fillable = [
        'enabled',
        'currency_id',
        'allow_wallet',
        'product_price_default',
    ];

    protected $with = ['currency'];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(new EnabledScope);
    }

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
                return $this->currency !== null ? $this->currency->displayName : '?';
            case 'name':
                return $this->currency !== null ? $this->currency->name : '?';
            case 'symbol':
                return $this->currency !== null ? $this->currency->symbol : '?';
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
            case 'name':
                return true;
            case 'symbol':
                return $this->currency !== null;
            default:
                return parent::__isset($name);
        }
    }

    /**
     * Disable the enabled scope, and also return the disabled entities.
     */
    public function scopeWithDisabled($query) {
        return $query->withoutGlobalScope(EnabledScope::class);
    }

    /**
     * Get the specified currency information.
     *
     * @return The currency.
     */
    public function currency() {
        return $this->belongsTo('App\Models\Currency');
    }

    /**
     * Get the economy this currency model is part of.
     *
     * @return The economy.
     */
    public function economy() {
        return $this->belongsTo('App\Models\Economy');
    }
}
