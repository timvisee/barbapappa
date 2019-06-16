<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Scopes\EnabledScope;
use App\Utils\EmailRecipient;

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

    // TODO: global unread/persistent scope

    public static function boot() {
        parent::boot();

        // Cascade delete to notificationable
        self::deleting(function($model){
            $model->notificationable()->delete();
        });
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
     * Build and return the URL for the wallet show page.
     *
     * @return string The wallet show URL.
     */
    // TODO: attempt to implement some eager loading of the economy model
    public function getUrlShow() {
        return route('community.wallet.show', [
            // TODO: can we use $this->economy->community_id here?
            'communityId' => $this->economy->community->human_id,
            'economyId' => $this->economy_id,
            'walletId' => $this->id,
        ]);
    }

    // TODO: delete notificationable morphs along with it
}
