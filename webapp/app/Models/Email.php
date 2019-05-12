<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Email model.
 *
 * @property int id
 * @property int user_id
 * @property string email
 * @property Carbon|null verified_at
 * @property string|null verified_ip
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Email extends Model {

    /**
     * A scope for selecting only verified email addresses.
     */
    public function scopeVerified($query) {
        return $query->where('verified_at', '!=', null);
    }

    /**
     * A scope for selecting only unverified email addresses.
     */
    public function scopeUnverified($query) {
        return $query->whereNull('verified_at');
    }

    /**
     * Get the user this email address belongs to.
     *
     * @return User The user.
     */
    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Get all email verification tokens that belong to this email address.
     *
     * @return The email verifications.
     */
    public function verifications() {
        return $this->hasMany('App\Models\EmailVerification');
    }

    /**
     * Check whether an email address is verified or not.
     *
     * @return bool True if verified, false if not.
     */
    public function isVerified() {
        return $this->verified_at != null;
    }
}
