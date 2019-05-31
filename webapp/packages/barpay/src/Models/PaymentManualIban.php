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

    /**
     * Create the paymentable part for a newly started payment, and attach it to
     * the payment.
     *
     * @param Payment $payment The payment to create it for, and to attach it to.
     * @param Service $service The payment service to use.
     *
     * @return Paymentable The created payment.
     */
    public static function startPaymentable(Payment $payment, Service $service) {
        // TODO: require to be in a transaction?

        // Get the serviceable
        $serviceable = $service->serviceable;

        // Build the paymentable for the payment
        $paymentable = new PaymentManualIban();
        $paymentable->payment_id = $payment->id;
        $paymentable->to_account_holder = $serviceable->account_holder;
        $paymentable->to_iban = $serviceable->iban;
        $paymentable->to_bic = $serviceable->bic;
        // TODO: somehow obtain the target iban here!
        $paymentable->from_iban = '';
        $paymentable->save();

        // Attach the paymentable to the payment
        $payment->setPaymentable($paymentable);

        return $paymentable;
    }
}
