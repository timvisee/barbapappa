<?php

namespace App\Models;

use App\Utils\EmailRecipient;
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

    protected $table = 'email';

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
        return $this->belongsTo(User::class);
    }

    /**
     * Get all email verification tokens that belong to this email address.
     *
     * @return The email verifications.
     */
    public function verifications() {
        return $this->hasMany(EmailVerification::class);
    }

    /**
     * Check whether an email address is verified or not.
     *
     * @return bool True if verified, false if not.
     */
    public function isVerified() {
        return $this->verified_at != null;
    }

    /**
     * Check whether the given text email address matches this email.
     *
     * @param string $email The email address.
     *
     * @return boolean True if matches, false if not.
     */
    public function isEmail($email) {
        return trim(strtolower($this->email)) == trim(strtolower($email));
    }

    /**
     * Build an EmailRecipient for this user email address.
     *
     * @param User $user The user this is for, or null to use the user linked to
     *      this mail address automatically.
     *
     * @return EmailRecipient The email recipient.
     */
    public function buildEmailRecipient($user = null) {
        return new EmailRecipient($this->email, $user ?? $this->user);
    }
}
