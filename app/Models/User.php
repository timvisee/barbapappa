<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * User model.
 *
 * @property int id
 * @property string password
 * @property string first_name
 * @property string last_name
 * @property-read string name
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property-read string email
 */
class User extends Model {

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

    /**
     * Get dynamic properties.
     *
     * @param string $name Property name.
     *
     * @return mixed|string Result.
     */
    public function __get($name) {
        switch ($name) {
            case 'name':
                return $this->first_name . ' ' . $this->last_name;
            case 'email':
                return $this->getPrimaryEmail()->email;
            default:
                return parent::__get($name);
        }
    }

    /**
     * Check whether dynamic properties exist.
     *
     * @param string $name Property name.
     *
     * @return bool True if exists, false if not.
     */
    public function __isset($name) {
        switch ($name) {
            case 'name':
                return true;
            case 'email':
                return $this->getPrimaryEmail() != null;
            default:
                return parent::__isset($name);
        }
    }

    public function posts() {
        return $this->hasMany('App\Models\Post');
    }

    public function emails() {
        return $this->hasMany('App\Models\Email');
    }

    /**
     * Check whether this user has any verified email addresses.
     *
     * @return bool True if the user has any verified email address, false if not.
     */
    public function hasVerifiedEmail() {
        return $this->emails()
                ->where('verified_at', '!=', null)
                ->first() != null;
    }

    /**
     * Get the primary email address of the user.
     *
     * @return Email|null Primary email address or null if the user doesn't have any.
     */
    public function getPrimaryEmail() {
        // TODO: Actually return the primary email address instead of the first one.
        return $this->emails()->first();
    }
}
