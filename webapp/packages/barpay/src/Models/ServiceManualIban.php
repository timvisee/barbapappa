<?php

namespace BarPay\Models;

use BarPay\Controllers\ServiceManualIbanController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Manual IBAN service class.
 *
 * This represents a payment service for a manual IBAN transfer.
 *
 * @property int id
 * @property int service_id
 * @property string account_holder Name of the account holder.
 * @property string iban IBAN to transfer to.
 * @property string|null bic Optional BIC corresponding to the IBAN.
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ServiceManualIban extends Model {

    protected $table = "service_manual_iban";

    /**
     * The controller to use for this service.
     */
    public const CONTROLLER = ServiceManualIbanController::class;

    /**
     * The payment model for this service.
     */
    public const PAYMENT_MODEL = PaymentManualIban::class;

    /**
     * The root for views related to this service.
     */
    public const VIEW_ROOT = 'barpay::service.manualiban';

    /**
     * Get a relation to the service this belongs to.
     *
     * @return Relation to the service.
     */
    public function service() {
        return $this->morphOne(Service::class, 'serviceable');
    }

    /**
     * Get the name for this service type.
     *
     * @return string Name for this service type.
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
        return __('barpay::service.manualiban.' . $key);
    }

    /**
     * Get the path for a view related to this service.
     *
     * @return string The path to the view.
     */
    public static function view($path) {
        return Self::VIEW_ROOT . '.' . $path;
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
    protected static function startPaymentable(Payment $payment, Service $service) {
        return (Self::PAYMENT_MODEL)::startPaymentable($payment, $service);
    }

    /**
     * Block direclty deleting.
     */
    public function delete() {
        throw new \Exception('cannot directly delete serviceable, delete the owning service instead');
    }
}
