<?php

namespace App\Models\Notifications;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Models\User;
use App\Perms\CommunityRoles;
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
 * Payment requires community admin action notification.
 *
 * @property int id
 * @property int payment_id
 * @property-read Payment payment
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class PaymentRequiresCommunityAction extends Model {

    use Notificationable;

    protected $table = 'notification_payment_requires_community';

    protected $with = ['notification'];

    public $persistent = true;

    /**
     * Create this notification for all users that are administrator in the
     * community a payment is made in.
     *
     * @param Payment $payment The payment requiring action.
     * @return Notification The created notification.
     */
    public static function notify(Payment $payment) {
        // TODO: prevent duplicates

        // Attempt to find the community this payment is made in
        $economy = $payment->findEconomy();
        if(is_null($economy))
            return [];
        $community = $economy->community;

        // List all community managers
        $managers = $community
            ->memberUsers()
            ->wherePivot('role', '>=', CommunityRoles::MANAGER)
            ->get();

        // Build a notification for each manager, return notifications
        return $managers
            ->map(function($manager) use($payment) {
                $notificationable = new Self();
                $notificationable->payment_id = $payment->id;
                return $notificationable->createNotification($manager);
            });
    }

    /**
     * Suppress this notification for a user.
     *
     * @param Payment $payment The payment.
     */
    public static function suppress(Payment $payment) {
        PaymentRequiresCommunityAction::where('payment_id', $payment->id)
            ->each(function($notificationable) {
                $notificationable->notification()->withoutGlobalScopes()->delete();
            });
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
            'kind' => __('notifications.paymentRequiresCommunityAction.kind'),
            'message' => __('notifications.paymentRequiresCommunityAction.message', ['amount' => $payment->formatCost()]),
            'actions' => [[
                'action' => 'review',
                'label' => __('misc.review'),
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
        case 'review':
            return route('payment.approve', ['paymentId' => $this->payment_id]);
        default:
            return null;
        }
    }
}
