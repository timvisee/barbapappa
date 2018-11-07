<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Session model.
 *
 * @property int id
 * @property int user_id
 * @property string token
 * @property string created_ip
 * @property Carbon expire_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Session extends Model {

    /**
     * The user this session belongs to.
     */
    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Check whether this session has expired.
     *
     * @return bool True if expired, false if not.
     */
    public function isExpired() {
        // Get the attribute value, and make sure it's valid
        $expireAt = $this->expire_at;
        if($expireAt == null)
            return true;

        // Check whether the time is expired
        return Carbon::parse($expireAt)->isPast();
    }

    /**
     * Invalidate this session.
     * This makes the session expire from this moment, so it can't be used anymore for authentication.
     * If the session has already expired, nothing is changed.
     */
    public function invalidate() {
        // Return if the session has already expired
        if($this->isExpired())
            return;

        // Invalidate the session now
        $this->expire_at = Carbon::now();
        $this->save();
    }
}
