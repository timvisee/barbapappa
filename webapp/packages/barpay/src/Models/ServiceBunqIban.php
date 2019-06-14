<?php

namespace BarPay\Models;

use App\Models\BunqAccount;
use App\Models\Currency;
use BarPay\Controllers\ServiceBunqIbanController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Bunq IBAN service class.
 *
 * This represents a payment service for a bunq IBAN transfer.
 *
 * @property int id
 * @property int service_id
 * @property string bunq_account_id The ID of the bunq account being used.
 * @property-read BunqAccount bunq_account The ID of the bunq account being used.
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ServiceBunqIban extends Model {

    use Serviceable;

    protected $table = "service_bunq_iban";

    /**
     * The controller to use for this service.
     */
    public const CONTROLLER = ServiceBunqIbanController::class;

    /**
     * The payment model for this service.
     */
    public const PAYMENT_MODEL = PaymentBunqIban::class;

    /**
     * The root for language values related to this service.
     */
    public const LANG_ROOT = 'barpay::service.bunqiban';

    /**
     * The root for views related to this service.
     */
    public const VIEW_ROOT = 'barpay::service.bunqiban';

    /**
     * Get a relation to the bunq account.
     *
     * @return Relation to the bunq account.
     */
    public function bunqAccount() {
        return $this->belongsTo(BunqAccount::class);
    }

    /**
     * Block direclty deleting.
     */
    public function delete() {
        throw new \Exception('cannot directly delete serviceable, delete the owning service instead');
    }

    /**
     * Check whether the given currency is supported.
     *
     * @param Currency $currency The currenty to check.
     *
     * @return bool True if supported, false if not.
     */
    public static function isSupportedCurrency(Currency $currency) {
        return $currency->code == 'EUR';
    }
}
