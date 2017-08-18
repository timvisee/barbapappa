<?php

namespace App;

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
class Email extends Model
{
    public function user() {
        return $this->belongsTo('App\User');
    }

    /**
     * Check whether an email address is verified or not.
     *
     * @return bool True if verified, false if not.
     */
    public function isVerified() {
        return $this->attributes['verified_at'] != null;
    }
}
