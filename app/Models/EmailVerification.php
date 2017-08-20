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
}
