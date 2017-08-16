<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
