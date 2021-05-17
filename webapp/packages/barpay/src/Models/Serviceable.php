<?php

namespace BarPay\Models;

// TODO: require Model implementation?
trait Serviceable {

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
     * @param bool [$admin=false] Name for administrators (more detailed).
     *
     * @return string Name for this service type.
     */
    public static function name($admin = false) {
        return Self::__($admin ? 'nameAdmin' : 'name');
    }

    /**
     * Get a translation for this payment.
     *
     * @return string|null The translation or null if non existent.
     */
    public static function __($key) {
        return __(Self::LANG_ROOT . '.' . $key);
    }

    /**
     * Get the path for a view related to this payment.
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
}
