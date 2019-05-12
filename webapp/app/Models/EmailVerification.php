<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Email verification model.
 *
 * @property int id
 * @property int email_id
 * @property string token
 * @property Carbon|null expire_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class EmailVerification extends Model {

    public function email() {
        return $this->belongsTo('App\Models\Email');
    }

    /**
     * Check whether the given token is valid.
     *
     * @param bool $token The token.
     *
     * @return bool True if the token is valid, false otherwise.
     */
    public function isValidToken($token) {
        return trim($this->token) == trim($token) && !empty($this->token);
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
}
