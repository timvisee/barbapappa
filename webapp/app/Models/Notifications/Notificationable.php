<?php

namespace App\Models\Notifications;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

// TODO: require Model implementation
trait Notificationable {

    /**
     * Get a relation to the notification this belongs to.
     *
     * @return Relation to the notification.
     */
    public function notification() {
        return $this->morphOne(Notification::class, 'notificationable');
    }

    /**
     * Create a notification for this notificationable.
     * This notificationable must not be saved yet.
     *
     * @param User $user The user this notification is for.
     *
     * @return Notification The created notification.
     */
    public function createNotification(User $user) {
        // The notificationable may not be in the database yet
        if(!is_null($this->id))
            throw new \Exception('Failed to create notification, notificationable is already in the database');

        // Create notification and notificationable in a database transaction
        $notification = null;
        $notificationable = $this;
        DB::transaction(function() use(&$notification, $notificationable, $user) {
            // Save the notificationable
            $notificationable->save();

            // Create a notification
            $notification = Notification::createForNotificationable(
                $user,
                $notificationable
            );
        });

        return $notification;
    }
}
