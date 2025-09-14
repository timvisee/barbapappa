<?php

namespace App\Models;

use App\Jobs\RenewBunqApiContext;
use App\Scopes\EnabledScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use bunq\Context\ApiContext;
use bunq\Context\BunqContext;
use bunq\Model\Core\NotificationFilterUrlMonetaryAccountInternal;
use bunq\Model\Generated\Endpoint\MonetaryAccountBankApiObject;
use bunq\Model\Generated\Object\NotificationFilterUrlObject;

/**
 * bunq account model.
 *
 * This specifies a bunq account (with a specific IBAN) which is dedicated to
 * this application, to use for automated payment processing.
 *
 * @property int id
 * @property int community_id
 * @property-read Community community
 * @property boolean enable_payments
 * @property boolean enable_checks
 * @property string|null name
 * @property string api_context_encrypted
 * @property string api_context
 * @property int monetary_account_id
 * @property string account_holder
 * @property string iban
 * @property string bic
 * @property int|null last_event_id
 * @property Carbon|null checked_at
 * @property Carbon|null renewed_at
 * @property Carbon|null deleted_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class BunqAccount extends Model {

    use SoftDeletes;

    /**
     * The number of seconds to start renewing the API context session in before
     * it expires.
     */
    const BUNQ_SESSION_EXPIRY_RENEW_PERIOD = ApiContext::TIME_TO_SESSION_EXPIRY_MINIMUM_SECONDS;

    protected $table = 'bunq_account';

    protected $casts = [
        'checked_at' => 'datetime',
        'renewed_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'community_id',
        'enable_payments',
        'enable_checks',
        'name',
        'account_holder',
        'iban',
        'bic'
    ];

    protected $hidden = [
        'api_context_encrypted',
        'api_context',
        'monetary_account_id',
    ];

    public static function boot() {
        parent::boot();

        // TODO: this is ambiguous, remove it, there are more 'enabled' fields
        static::addGlobalScope(new EnabledScope('enable_payments'));
    }

    /**
     * Scope a query to filter only to bunq accounts that have periodic checks
     * enabled.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeChecksEnabled($query) {
        return $query->where('enable_checks', true);
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
        // Obtain the API context
        $apiContext = $this->api_context;

        // Renew the API context if (close to) expired
        $this->checkRenewApiContext($apiContext);

        // Load the bunq context for this request
        BunqContext::loadApiContext($apiContext);
    }

    /**
     * Check whether the bunq API context should be renewed, and renew it if
     * it's close to expiry.
     *
     * This basically ensures a valid API context is used. When the context is
     * already expired, it is immediately renewed in a synchronous manner. If
     * it's close to expiry, a job is spawned to renew it in the background.
     *
     * The given API context is updated in-place if it is renewed synchronously
     * because it had already expired.
     *
     * @param ApiContext &$apiContext The current bunq API context.
     */
    private function checkRenewApiContext(&$apiContext) {
        // Determine in how many seconds the session expires
        $expireAt = $apiContext->getSessionContext()->getExpiryTime()->getTimestamp();
        $expireIn = $expireAt - time();

        // Return if not close to expiry
        if($expireIn > Self::BUNQ_SESSION_EXPIRY_RENEW_PERIOD)
            return;

        // Immediately renew session if already almost expired, refresh API context
        if($expireIn <= 10) {
            RenewBunqApiContext::dispatchSync($this);
            $this->renewed_at = now();
            $this->save();
            $this->refresh();
            $apiContext = $this->api_context;
            return;
        }

        // Do not spawn renewal job if already recently renewed/renewing
        if(!is_null($this->renewed_at) && $this->renewed_at >= now()->subSeconds(Self::BUNQ_SESSION_EXPIRY_RENEW_PERIOD))
            return;

        // Dispatch a job to renew the session, update the renew time
        RenewBunqApiContext::dispatch($this);
        $this->renewed_at = now();
        $this->save();
    }

    /**
     * Get the monetary account that is specified for this bunq account.
     * You must have called `loadBunqContext()` first.
     *
     * TODO: automatically call `loadBunqContext` if currently in a different
     * context.
     *
     * @return MonetaryAccountBankApiObject The monetary bank account, or an error.
     */
    public function fetchMonetaryAccount() {
        return MonetaryAccountBankApiObject::get($this->monetary_account_id)->getValue();
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
        if(is_url_secure()) {
            $filters[] = new NotificationFilterUrlObject(
                'PAYMENT',
                route('callback.bunq')
            );
            $filters[] = new NotificationFilterUrlObject(
                'BUNQME_TAB',
                route('callback.bunq')
            );
        }

        // Set the filters
        NotificationFilterUrlMonetaryAccountInternal::createWithListResponse(
            $this->monetary_account_id,
            $filters,
        );

        return $message;
    }
}
