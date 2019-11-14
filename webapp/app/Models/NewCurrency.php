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
 * Currency model.
 *
 * This defines the currencies available in an economy.
 *
 * @property int id
 * @property int economy_id
 * @property-read Economy economy
 * @property string name
 * @property string|null code
 * @property string symbol
 * @property string format
 * @property bool enabled
 * @property bool allow_wallet
 * @property Carbon deleted_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class NewCurrency extends Model {

    protected $table = 'new_currency';

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

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
     * Disable the enabled scope, and also return the disabled entities.
     */
    public function scopeWithDisabled($query) {
        return $query->withoutGlobalScope(EnabledScope::class);
    }

    /**
     * Get a relation to the economy this currency is in.
     *
     * @return Relation to the economy.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * Get a list of known currency codes following ISO 4217.
     * These are stored in 'resources/data/currency-codes.txt'.
     *
     * @return array
     */
    public static function currencyCodeList() {
        return array_filter(explode(
                "\n",
                file_get_contents(resource_path('data/currency-codes.txt'))
            ),
            function($item) {
                return !empty(trim($item));
            }
        );
    }
}
