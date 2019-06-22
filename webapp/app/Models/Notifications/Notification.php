<?php

namespace App\Models\Notifications;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Notification model.
 *
 * This represents a notification for a user.
 *
 * @property int id
 * @property int user_id
 * @property-read User user
 * @property int|null notificationable_id
 * @property string notificationable_type
 * @property bool persistent
 * @property Carbon read_at
 * @property Carbon expire_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Notification extends Model {

    protected $table = "notifications";

    protected $casts = [
        'read_at' => 'datetime',
        'expire_at' => 'datetime',
    ];

    /**
     * Default number of seconds for a notification to expire.
     *
     * @var int
     */
    public const NOTIFICATION_EXPIRE =  30 * 24 * 60 * 60;

    public static function boot() {
        parent::boot();

        // Add global scopes
        static::addGlobalScope('user', function(Builder $builder) {
            if(($user = barauth()->getUser()) != null)
                $builder->forUser($user);
        });
        static::addGlobalScope('visible', function(Builder $builder) {
            $builder->visible();
        });
        static::addGlobalScope('order', function(Builder $builder) {
            $builder->latest('updated_at');
        });

        // Cascade delete to notificationable
        static::deleting(function($model) {
            $model->notificationable()->delete();
        });
    }

    /**
     * A scope for notifications targeted to the given user.
     *
     * @param \Builder $query The query builder.
     */
    public function scopeForUser($query, User $user) {
        return $query->where('user_id', $user->id);
    }

    /**
     * A scope for all notifications that should be visible to the user.
     * This includes not jsut unread notifications, but also persistent
     * notifications.
     *
     * @param \Builder $query The query builder.
     */
    public function scopeVisible($query) {
        return $query->unread()->orWhere('persistent', true);
    }

    /**
     * A scope for unread notification items.
     *
     * It is recommended to use the `visible()` scope instead, which also
     * includes persistent notifications.
     *
     * @param \Builder $query The query builder.
     */
    public function scopeUnread($query) {
        return $query->where('read_at', null);
    }

    /**
     * Get a relation to the user this belogns to.
     *
     * @return The user this belongs to.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a relation to the specific notification type data related to the used
     * notificationable.
     *
     * @return Relation to the notification type data related to the used notificationable.
     */
    public function notificationable() {
        return $this->morphTo();
    }

    /**
     * Mark this notification as read.
     */
    public function markAsRead() {
        $now = now();
        if($this->read_at == null || $this->read_at > $now) {
            $this->read_at = $now;
            $this->save();
        }
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
        $data = $this->notificationable->viewData();
        $data['notification'] = $this;
        return $data;
    }

    /**
     * Get the URL for an action with the given name.
     *
     * This method is expensive for many notifications, be careful.
     *
     * @param string $action The action name.
     * @param bool $markAsRead Mark this notification as read.
     *
     * @return string|null The action URL, or null if invalid.
     */
    public function getActionUrl($action, $markAsRead) {
        // Mark as read
        if($markAsRead)
            $this->markAsRead();

        // Get the action URL from the notificationable
        return $this->notificationable->getActionUrl($action);
    }

    /**
     * Create a notification which which owns the given notificationable.
     *
     * It is not recommended to call this method directly. Create a
     * Notificationable instance, and use `createNotification()` instead.
     *
     * @param User $user The user to create this notification for.
     * @param Notificationable $notificationable The notificationable to create
     *      the notification for.
     * @return Notification The created notification.
     */
    protected static function createForNotificationable(User $user, $notificationable) {
        // Create the notification
        $notification = new Notification();
        $notification->user_id = $user->id;
        $notification->notificationable_id = $notificationable->id;
        $notification->notificationable_type = get_class($notificationable);
        $notification->persistent = false;
        $notification->expire_at = now()->addSeconds(Self::NOTIFICATION_EXPIRE);
        $notification->save();

        return $notification;
    }
}
