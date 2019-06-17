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
            // TODO: translate this
            'message' => 'Your ' . $payment->formatCost() . ' top-up completed',

            'actions' => [[
                // TODO: translate this
                'name' => 'View',
                'url' => route('payment.show', ['paymentId' => $this->payment_id]),
            ]],
        ];
    }
}
