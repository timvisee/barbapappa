<?php

namespace App;

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

    public function user() {
        return $this->belongsTo('App\User');
    }

    /**
     * Check whether this session has expired.
     *
     * @return bool True if expired, false if not.
     */
    public function isExpired() {
        // Get the attribute value, and make sure it's valid
        $expireAt = $this->attributes['expire_at'];
        if($expireAt == null)
            return true;

        // Check whether the time is expired
        return Carbon::parse($expireAt)->isPast();
    }
}
