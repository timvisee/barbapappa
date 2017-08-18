<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * User model.
 *
 * @property int id
 * @property string password
 * @property string first_name
 * @property string last_name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    public function posts() {
        return $this->hasMany('App\Post');
    }

    public function emails() {
        return $this->hasMany('App\Email');
    }

    /**
     * Check whether this user has any verified email addresses.
     *
     * @return bool True if the user has any verified email address, false if not.
     */
    public function hasVerifiedEmail() {
        return Email::where('user_id', '=', $this->id)
                ->where('verified_at', '!=', null)
                ->first() != null;
    }
}
