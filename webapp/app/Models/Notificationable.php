<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
