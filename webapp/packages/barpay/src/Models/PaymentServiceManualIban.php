<?php

namespace BarPay\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Manual IBAN payment service.
 *
 * This represents a payment service for a manual IBAN transfer.
 *
 * @property int id
 * @property int payment_service_id
 * @property string account_holder Name of the account holder.
 * @property string iban IBAN to transfer to.
 * @property string|null bic Optional BIC corresponding to the IBAN.
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class PaymentServiceManualIban extends Model {

    protected $table = "payment_service_manual_iban";

    /**
     * Get a relation to the payment service this belongs to.
     *
     * @return Relation to the payment service.
     */
    public function paymentService() {
        return $this->morphOne(PaymentService::class, 'serviceable');
    }

    /**
     * Get the name for this payment service type.
     *
     * @return string Name for this payment service type.
     */
    public static function name() {
        return Self::__('name');
    }

    /**
     * Get a translation for this service.
     *
     * @return string|null The translation or null if non existent.
     */
    public static function __($key) {
        return __('paymentservice.manualiban.' . $key);
    }
}
