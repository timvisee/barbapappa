<?php

namespace BarPay\Models;

use App\Scopes\EnabledScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Payment service model.
 *
 * This represents a payment service.
 *
 * @property int id
 * @property int economy_id
 * @property int serviceable_id
 * @property string serviceable_type
 * @property->read mixed serviceable
 * @property boolean enabled
 * @property int deposit_min
 * @property int deposit_max
 * @property int withdraw_min
 * @property int withdraw_max
 * @property Carbon|null deleted_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class PaymentService extends Model {

    use SoftDeletes;

    protected $table = "payment_services";

    const STATE_PENDING = 1;
    const STATE_PROCESSING = 2;
    const STATE_COMPLETED = 3;
    const STATE_REVOKED = 4;
    const STATE_REJECTED = 5;
    const STATE_FAILED = 6;

    /**
     * Payment service types.
     */
    public const SERVICEABLES = [
        PaymentServiceManualIban::class,
    ];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(new EnabledScope);
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
        return $query->where('deposit_max', '>', 0);
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
        return $query->where('withdraw_max', '>', 0);
    }

    /**
     * Get a relation to the specific payment service type data.
     *
     * @return Relation to the payment service type data.
     */
    public function serviceable() {
        return $this->morphTo();
    }
}
