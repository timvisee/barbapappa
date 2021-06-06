<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Password reset model.
 *
 * @property int id
 * @property int user_id
 * @property string token
 * @property bool used
 * @property Carbon|null expire_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class PasswordReset extends Model {

    protected $table = 'password_reset';

    /**
     * A scope for password reset tokens that have expired.
     *
     * @param \Builder $query The query builder.
     */
    public function scopeExpired($query) {
        return $query
            ->where(function($query) {
                $query->whereNull('expire_at')
                      ->orWhere('expire_at', '<=', now());
            });
    }

    /**
     * Get relation to user this password reset token belongs to.
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
}
