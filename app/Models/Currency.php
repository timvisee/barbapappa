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
 * User model.
 *
 * @property int id
 * @property string password
 * @property string first_name
 * @property string last_name
 * @property-read string name
 * @property int role
 * @property string|null locale
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
        'password',
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

    public function sessions() {
        return $this->hasMany('App\Models\Session');
    }

    public function emails() {
        return $this->hasMany('App\Models\Email');
    }

    public function passwordResets() {
        return $this->hasMany('App\Models\PasswordReset');
    }

    /**
     * A list of communities this user has joined.
     *
     * @param array [$pivotColumns] An array of pivot columns to include.
     * @param boolean [$withTimestamps=true] True to include timestamp columns.
     *
     * @return A list of joined communities.
     */
    public function communities($pivotColumns = [], $withTimestamps = true) {
        // Query relation
        $query = $this->belongsToMany(
                'App\Models\Community',
                'community_user',
                'user_id',
                'community_id'
            );

        // With pivot columns
        if(!empty($pivotColumns))
            $query = $query->withPivot($pivotColumns);

        // With timestamps
        if($withTimestamps)
            $query = $query->withTimestamps();

        return $query;
    }

    /**
     * A list of bars this user has joined.
     *
     * @param array [$pivotColumns] An array of pivot columns to include.
     * @param boolean [$withTimestamps=true] True to include timestamp columns.
     *
     * @return A list of joined bars.
     */
    public function bars($pivotColumns = [], $withTimestamps = true) {
        // Query relation
        $query = $this->belongsToMany(
                'App\Models\Bar',
                'bar_user',
                'user_id',
                'bar_id'
            );

        // With pivot columns
        if(!empty($pivotColumns))
            $query = $query->withPivot($pivotColumns);

        // With timestamps
        if($withTimestamps)
            $query = $query->withTimestamps();

        return $query;
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

    /**
     * Invalidate sessions for this user.
     *
     * @param bool $current True to invalidate the current session, false to keep it alive.
     * @param bool $other True to invalidate all other sessions, false to keep it alive.
     */
    public function invalidateSessions($current, $other) {
        // Return if nothing should be invalidated
        if(!$current && !$other)
            return;

        // Get the current session
        $currentSession = barauth()->getAuthState()->getSession();

        // Invalidate other user sessions
        $this->sessions()->get()->each(function($session) use($current, $other, $currentSession) {
            // Invalidate the current session
            if($current && $currentSession != null && $currentSession->id == $session->id) {
                $session->invalidate();
                return;
            }

            // Invalidate other sessions
            if($other && ($currentSession == null || $currentSession->id != $session->id)) {
                $session->invalidate();
                return;
            }
        });
    }

    /**
     * Check if the given user password is valid.
     *
     * TODO: Should we throttle this?
     *
     * @param string $password Password to check.
     * @param bool $rehash=true True to automatically rehash the password if needed.
     *
     * @return bool True if the password is valid, false if not.
     */
    public function checkPassword($password, $rehash = true) {
        // Make sure the given password isn't null
        if($password == null)
            return false;

        // Check whether the given password is valid
        if(!Hash::check($password, $this->password))
            return false;

        // Rehash the password if needed
        if($rehash && Hash::needsRehash($this->password)) {
            // Log a message, then change the password to rehash it
            Log::info('Rehashing password for user with ID ' . $this->id);
            $this->changePassword($password, false);
        }

        // The password seems to be valid
        return true;
    }

    /**
     * Change the password for this user.
     *
     * @param string $password New password (not hashed).
     * @param bool $sendMail True to send a mail about the password change, false if not.
     *
     * @throws \Exception Throws if the given password is invalid.
     */
    public function changePassword($password, $sendMail) {
        // Make sure the password isn't null
        if($password == null)
            throw new \Exception('New password for user is null');

        // TODO: Validate the password

        // Change the password
        $this->password = Hash::make($password);
        $this->save();

        // Send an email about the password change
        if($sendMail) {
            // Get the primary email address for the user
            $email = $this->getPrimaryEmail();

            // Send an additional reset token to allow the user to revert the password if the change was unwanted
            if($email != null) {
                // Create an additional reset token to allow the user to revert the password change
                $extraReset = PasswordResetManager::create($this);

                // Create a mailable
                $recipient = new EmailRecipient($email, $this);
                $mailable = new Reset($recipient, $extraReset);

                // Send the mailable
                Mail::send($mailable);
            }
        }
    }
}
