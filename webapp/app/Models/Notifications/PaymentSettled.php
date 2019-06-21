<?php

namespace App\Models\Notifications;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Models\User;
use App\Scopes\EnabledScope;
use App\Utils\EmailRecipient;
use BarPay\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Payment settled notification.
 *
 * @property int id
 * @property int payment_id
 * @property-read Payment payment
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class PaymentSettled extends Model {

    use Notificationable;

    protected $table = "notifications_payment_settled";

    protected $with = ['notification'];

    /**
     * Create this notification for a user.
     *
     * @param Payment $payment The settled payment.
     * @return Notification The created notification.
     */
    public static function notify(Payment $payment) {
        $notificationable = new Self();
        $notificationable->payment_id = $payment->id;
        return $notificationable->createNotification($payment->user);
    }

    /**
     * Get a relation to the payment.
     *
     * @return A relation to the payment.
     */
    public function payment() {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the view data for this notification.
     * This returns an array of view data for the notification.
     *
     * This method is expensive for many notifications, be careful.
     *
     * @return array Array of view data.
     */
    public function viewData() {
        // Gather facts
        $payment = $this->payment;

        // Return view data
        return [
            'kind' => __('notifications.paymentSettled.kind'),
            'message' => __('notifications.paymentSettled.message', ['amount' => $payment->formatCost()]),

            'actions' => [[
                'action' => 'view',
                'label' => __('misc.view'),
            ]],
        ];
    }

    /**
     * Get the URL for an action with the given name.
     *
     * @param string $action The action name.
     *
     * @return string|null The action URL, or null if invalid.
     */
    public function getActionUrl($action) {
        switch($action) {
        case 'view':
            return route('payment.show', ['paymentId' => $this->payment_id]);
        default:
            return null;
        }
    }
}
