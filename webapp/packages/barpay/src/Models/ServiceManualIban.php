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
}
