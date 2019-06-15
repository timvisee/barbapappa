<?php

namespace BarPay\Models;

use App\Models\Currency;
use App\Models\Economy;
use App\Models\User;
use App\Scopes\EnabledScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Service model.
 *
 * This represents a payment service.
 *
 * @property int id
 * @property int economy_id
 * @property int serviceable_id
 * @property string serviceable_type
 * @property-read mixed serviceable
 * @property boolean enabled
 * @property int currency_id
 * @property-read Currency currency
 * @property boolean deposit
 * @property boolean withdraw
 * @property Carbon|null deleted_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Service extends Model {

    use SoftDeletes;

    protected $table = "services";

    protected $fillable = [
        'serviceable_id',
        'serviceable_type',
        'enabled',
        'deposit',
        'withdraw',
        'currency_id',
    ];

    protected $with = ['serviceable'];

    const STATE_PENDING = 1;
    const STATE_PROCESSING = 2;
    const STATE_COMPLETED = 3;
    const STATE_REVOKED = 4;
    const STATE_REJECTED = 5;
    const STATE_FAILED = 6;

    /**
     * Service types.
     */
    public const SERVICEABLES = [
        ServiceBunqMeTab::class,
        ServiceBunqIban::class,
        ServiceManualIban::class,
    ];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(new EnabledScope);
    }

    /**
     * Disable the enabled scope, and also return the disabled entities.
     */
    public function scopeWithDisabled($query) {
        return $query->withoutGlobalScope(EnabledScope::class);
    }

    /**
     * Scope a query a specific state.
     * this platform.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeState($query, $state) {
        return $query->where('state', $state);
    }

    /**
     * Scope a query to filter services that don't allow depositing money to
     * this platform.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSupportsDeposit($query) {
        return $query->where('deposit', true);
    }

    /**
     * Scope a query to filter services that don't allow withdrawing from this
     * platform.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSupportsWithdraw($query) {
        return $query->where('withdraw', true);
    }

    /**
     * Get a relation to the economy this belongs to.
     *
     * @return Relation to the economy.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * Get a relation to all payments made with this service.
     *
     * @return Relation to all payments.
     */
    public function payments() {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get a relation to the specific payment service type data.
     *
     * @return Relation to the payment service type data.
     */
    public function serviceable() {
        return $this->morphTo();
    }

    /**
     * Get the display name for this service.
     * This will be shown both to administrators and to regular users.
     *
     * @return string Display name.
     */
    public function displayName() {
        return $this->serviceable::name();
    }

    /**
     * Set the serviceable attached to this service.
     * This is only allowed when no serviceable is set yet.
     *
     * @param mixed The serviceable to attach.
     * @param bool [$save=true] True to immediately save this model, false if
     * not.
     *
     * @throws \Exception Throws if a serviceable was already set.
     */
    public function setServiceable($serviceable, $save = true) {
        // Assert no serviceable is set yet
        if(!empty($this->serviceable_id) || !empty($this->serviceable_type))
            throw new \Exception('Could not link serviceable to payment service, it has already been set');

        // Set the serviceable
        $this->serviceable_id = $serviceable->id;
        $this->serviceable_type = get_class($serviceable);
        if($save)
            $this->save();
    }

    /**
     * Start a new payment with this service.
     *
     * @param Currency $currency The currency to use.
     * @param float $amount The payment amount.
     * @param User $user User the payment is for.
     *
     * @return Payment The created payment.
     */
    public function startPayment(Currency $currency, float $amount, User $user) {
        return Payment::startNew($this, $currency, $amount, $user);
    }
}
