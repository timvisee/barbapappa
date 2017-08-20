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

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
