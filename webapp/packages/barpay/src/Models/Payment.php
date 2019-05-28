<?php

namespace BarPay\Models;

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
 * @property int payment_service_id
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
     * Get the relation to the used payment service linked to this payment.
     *
     * @return Relation to the used payment service.
     */
    public function paymentService() {
        return $this->belongsTo(PaymentService::class);
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
}
