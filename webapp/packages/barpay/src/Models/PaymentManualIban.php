<?php

namespace BarPay\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Manual IBAN payment data class.
 *
 * This represents a payment data for a manual IBAN transfer.
 *
 * @property int id
 * @property int payment_id
 * @property string iban IBAN to transfer to.
 * @property string ref A reference code.
 * @property datetime|null transferred_at When the user manuall transferred if done.
 * @property datetime|null confirmed_at When the manual transfer was confirmed by the counter party if done.
 * @property string|null bic Optional BIC corresponding to the IBAN.
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class PaymentManualIban extends Model {

    protected $table = "payment_manual_iban";

    /**
     * Get a relation to the payment this belongs to.
     *
     * @return Relation to the payment.
     */
    public function payment() {
        return $this->morphOne(Payment::class, 'paymentable');
    }
}
