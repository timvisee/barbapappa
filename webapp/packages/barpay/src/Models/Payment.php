<?php

namespace BarPay\Models;

use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Payment model.
 *
 * This represents a payment.
 *
 * @property int id
 * @property int state
 * @property int service_id
 * @property int paymentable_id
 * @property string paymentable_type
 * @property-read mixed paymentable
 * @property string|null reference
 * @property decimal amount
 * @property int currency_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Payment extends Model {

    protected $table = "payments";

    const STATE_PENDING = 1;
    const STATE_PROCESSING = 2;
    const STATE_COMPLETED = 3;
    const STATE_REVOKED = 4;
    const STATE_REJECTED = 5;
    const STATE_FAILED = 6;

    /**
     * Get the relation to the used service linked to this payment.
     *
     * @return Relation to the used service.
     */
    public function service() {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the used currency.
     *
     * @return The currency.
     */
    // TODO: is this correct?
    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Format the money amount for this payment.
     * This shows a neutral value by default.
     *
     * If the user pays money, the returned value is positive. If the user
     * receives/deposits money, the returned value is negative.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     * @param boolean [$invert=false] True to invert the cost value.
     * @param array [$options=[]] List of formatting options.
     *
     * @return string Formatted amount.
     */
    public function formatCost($format = BALANCE_FORMAT_PLAIN, $options = ['neutral' => true]) {
        return $this->currency->formatAmount($this->amount, $format, $options);
    }

    /**
     * Get the display name for the current payment state.
     *
     * @return State display name.
     */
    // TODO: add these in translation files
    public function stateName() {
        // Get the state key here
        $key = [
            Self::STATE_PENDING => 'pending',
            Self::STATE_PROCESSING => 'processing',
            Self::STATE_COMPLETED => 'completed',
            Self::STATE_REVOKED => 'revoked',
            Self::STATE_REJECTED => 'rejected',
            Self::STATE_FAILED => 'failed',
        ][$this->state];
        if(empty($key))
            throw new \Exception("Unknown payment state, cannot get state name");

        // Translate and return
        return __('pages.payments.state.' . $key);
    }

    /**
     * Get a relation to the specific payment type data related to the used
     * payment service.
     *
     * @return Relation to the payment type data related to the used payment
     * service.
     */
    public function paymentable() {
        return $this->morphTo();
    }

    /**
     * Set the paymentable attached to this service.
     * This is only allowed when no paymentable is set yet.
     *
     * @param mixed The paymentable to attach.
     * @param bool [$save=true] True to immediately save this model, false if
     * not.
     *
     * @throws \Exception Throws if a paymentable was already set.
     */
    public function setPaymentable($paymentable, $save = true) {
        // Assert no paymentable is set yet
        if(!empty($this->paymentable_id) || !empty($this->paymentable_type))
            throw new \Exception('Could not link paymentable to payment, it has already been set');

        // Set the paymentable
        $this->paymentable_id = $paymentable->id;
        $this->paymentable_type = get_class($paymentable);
        if($save)
            $this->save();
    }

    /**
     * Start a new payment with the given service, currency and amount.
     *
     * @param Service $service The payment service to use.
     * @param Currency $currency The currency to use.
     * @param float $amount The payment amount.
     *
     * @return Payment The created payment.
     */
    public static function startNew(Service $service, Currency $currency, float $amount) {
        // TODO: require to be in a transaction?

        // TODO: assert this payment service can be used with this currency and amount
        // TODO: assert amount is 0.01 or higher, or should we allow negative as well?

        // Build a new payment
        $payment = new Payment();
        $payment->state = Payment::STATE_PENDING;
        $payment->service_id = $service->id;
        $payment->paymentable_id = 0;
        $payment->paymentable_type = '';
        $payment->currency_id = $currency->id;
        $payment->money = $amount;
        $payment->save();

        // Build the paymentable and attach it to the payment
        $paymentable = $service->serviceable::startPaymentable($payment, $service);

        return $payment;
    }
}
