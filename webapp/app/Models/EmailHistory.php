<?php

namespace App\Models;

use App\Utils\EmailRecipient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Email history model.
 *
 * @property int id
 * @property int user_id
 * @property int type
 * @property Carbon|null last_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class EmailHistory extends Model {

    /**
     * Email type: user wallet balance update
     */
    const TYPE_BALANCE_UPDATE = 1;

    /**
     * A scope for selecting a specific email type.
     */
    public function scopeType($query, int $type) {
        return $query->where('type', $type);
    }

    /**
     * Get the user this email address belongs to.
     *
     * @return User The user.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Update the last time an email of this type was sent.
     *
     * The current time will be used.
     */
    public function updateLast() {
        $this->last_at = now();
        $this->save();
    }
}
