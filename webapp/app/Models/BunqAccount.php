<?php

namespace App\Models;

use App\Scopes\EnabledScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use bunq\Context\ApiContext;
use bunq\Context\BunqContext;
use bunq\Model\Generated\Endpoint\MonetaryAccountBank;
use bunq\Model\Generated\Object\NotificationFilter;

/**
 * bunq account model.
 *
 * This specifies a bunq account (with a specific IBAN) which is dedicated to
 * this application, to use for automated payment processing.
 *
 * @property int id
 * @property int community_id
 * @property-read Community community
 * @property boolean enabled
 * @property string|null name
 * @property string api_context_encrypted
 * @property string api_context
 * @property int monetary_account_id
 * @property string account_holder
 * @property string iban
 * @property string bic
 * @property Carbon|null deleted_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class BunqAccount extends Model {

    use SoftDeletes;

    protected $table = "bunq_accounts";

    protected $fillable = ['community_id', 'enabled', 'name', 'account_holder', 'iban', 'bic'];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(new EnabledScope);
    }

    /**
     * Get the relation to the economy this product is part of.
     * This might be null if this account is not linked to a specific community.
     *
     * @return Relation to the community this product is part of.
     */
    public function community() {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the bunq API context for this linked account.
     * This automatically decrypts the `api_context_encrypted` field.
     *
     * This context should be handled with care!
     *
     * @return string bunq API context.
     */
    public function getApiContextAttribute() {
        return ApiContext::fromJson(
            Crypt::decryptString($this->api_context_encrypted)
        );
    }

    /**
     * Set the bunq API token for this account.
     * This automatically encrypts the context to the `api_context_encrypted` field.
     *
     * @param string $token The bunq API token.
     */
    public function setApiContextAttribute(ApiContext $api_context) {
        $this->api_context_encrypted = Crypt::encryptString(
            $api_context->toJson()
        );
    }

    /**
     * A function to load a bunq context for this bunq account.
     */
    public function loadBunqContext() {
        BunqContext::loadApiContext($this->api_context);
    }

    /**
     * Get the monetary account that is specified for this bunq account.
     * You must have called `loadBunqContext()` first.
     *
     * TODO: automatically call `loadBunqContext` if currently in a different
     * context.
     *
     * @return MonetaryAccountBank The monetary bank account, or an error.
     */
    public function fetchMonetaryAccount() {
        return MonetaryAccountBank::get($this->monetary_account_id)->getValue();
    }

    /**
     * Update the settings for the bunq account required to properly function.
     *
     * The bunq API context must be loaded first.
     *
     * @return string|null An optional warning message.
     */
    public function updateBunqAccountSettings() {
        // A warning to return
        $message = null;

        // Build a list of filters to use if we have HTTPS, error otherwise
        $filters = [];
        if(!is_url_secure()) {
            $filters[] = new NotificationFilter(
                'URL',
                route('callback.bunq'),
                'PAYMENT'
            );
            $filters[] = new NotificationFilter(
                'URL',
                route('callback.bunq'),
                'BUNQME_TAB'
            );
        } else
            $message = __('pages.bunqAccounts.noHttpsNoCallbacks');

        // Set the filters
        MonetaryAccountBank::update(
            $this->monetary_account_id,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $filters,
            null,
            []
        );

        return $message;
    }
}
