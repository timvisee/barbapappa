<?php

namespace App\Models;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Utils\EmailRecipient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Community model.
 *
 * @property int id
 * @property string name
 * @property bool visible
 * @property bool public
 * @property string|null password
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Community extends Model {

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    // /**
    //  * Get a list of users that joined this community.
    //  *
    //  * @return List of joined users.
    //  */
    // public function users() {
    //     return $this->hasMany('App\Models\Email');
    // }

    /**
     * Get a list of economies that are part of this community.
     *
     * @return List of economies.
     */
    public function economies() {
        return $this->hasMany('App\Models\Economy');
    }

    /**
     * Check whether this community has a password specified.
     *
     * @return bool True if specified, false if not or if empty.
     */
    public function hasPassword() {
        return !empty($this->password);
    }
}
