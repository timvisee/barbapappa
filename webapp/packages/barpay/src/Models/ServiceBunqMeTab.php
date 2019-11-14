<?php

namespace BarPay\Models;

use App\Models\BunqAccount;
use App\Models\NewCurrency;
use BarPay\Controllers\ServiceBunqMeTabController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * BunqMe Tab service class.
 *
 * This represents a payment service for a BunqMe Tab payment request.
 *
 * @property int id
 * @property string bunq_account_id The ID of the bunq account being used.
 * @property-read BunqAccount bunq_account The ID of the bunq account being used.
 * @property string account_holder The account holder to forward collected payments to.
 * @property string iban The IBAN to forward collected payments to.
 * @property string bic The BIC to format collected payments to.
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ServiceBunqMeTab extends Model {

    use Serviceable;

    protected $table = "service_bunqme_tab";

    /**
     * The controller to use for this service.
     */
    public const CONTROLLER = ServiceBunqMeTabController::class;

    /**
     * The payment model for this service.
     */
    public const PAYMENT_MODEL = PaymentBunqMeTab::class;

    /**
     * The root for language values related to this service.
     */
    public const LANG_ROOT = 'barpay::service.bunqmetab';

    /**
     * The root for views related to this service.
     */
    public const VIEW_ROOT = 'barpay::service.bunqmetab';

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
     * @param NewCurrency $currency The currenty to check.
     *
     * @return bool True if supported, false if not.
     */
    public static function isSupportedCurrency(NewCurrency $currency) {
        return $currency->code == 'EUR';
    }
}
