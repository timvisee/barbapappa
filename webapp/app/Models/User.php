<?php

namespace App\Models;

use App\Facades\LangManager;
use App\Mail\Password\Disabled;
use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Utils\EmailRecipient;
use BarPay\Models\Payment;
use Carbon\Carbon;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * $property bool notify_low_balance
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property-read string email
 */
class User extends Model implements HasLocalePreference {

    use HasFactory;

    protected $table = 'user';

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

    protected $casts = [
        'notify_low_balance' => 'boolean',
    ];

    /**
     * Request users to verify their email address after this number of seconds.
     */
    const REQUEST_MAIL_VERIFY_AFTER = 7 * 24 * 60 * 60;

    public static function boot() {
        parent::boot();

        // Cascade delete to economy members if it has no linked import alias
        static::deleting(function($model) {
            // TODO: do this through economy member class
            foreach($model->economyMembers as $member) {
                if($member->aliases()->limit(1)->count() > 0) {
                    $member->user_id = null;
                    $member->save();
                } else
                    $member->delete();
            }
        });
    }

    /**
     * Get dynamic properties.
     *
     * @param string $name Property name.
     *
     * @return mixed|string Result.
     */
    public function __get($name) {
        switch($name) {
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
        switch($name) {
            case 'name':
                return true;
            case 'email':
                return $this->getPrimaryEmail() != null;
            default:
                return parent::__isset($name);
        }
    }

    /**
     * Scope a query to only include users relevant to the given search
     * query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $search The search query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search) {
        // return $query
        //     // ->selectRaw("CONCAT(user.first_name, ' ', users.last_name) AS name")
        //     // ->where('name', 'LIKE', '%' . escape_like($search) . '%')
        //     ->where('first_name', 'LIKE', '%' . escape_like($search) . '%')
        //     ->orWhere('last_name', 'LIKE', '%' . escape_like($search) . '%');

        // Search for each word separately in the first/last name fields
        return $query->where(function($query) use($search) {
            foreach(explode(' ', $search) as $word)
                if(!empty($word))
                    $query->where('first_name', 'LIKE', '%' . escape_like($word) . '%')
                        ->orWhere('last_name', 'LIKE', '%' . escape_like($word) . '%');
        });
    }

    public function sessions() {
        return $this->hasMany(Session::class);
    }

    public function emails() {
        return $this->hasMany(Email::class);
    }

    public function email_history() {
        return $this->hasMany(EmailHistory::class);
    }

    public function notifications() {
        return $this->hasMany(Notification::class);
    }

    public function passwordResets() {
        return $this->hasMany(PasswordReset::class);
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
                'community_member',
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
                'bar_member',
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
     * Get a relation to all economy members this user is.
     *
     * @return Relation to economy members.
     */
    public function economyMembers() {
        return $this->hasMany(EconomyMember::class);
    }

    /**
     * Get the wallets owned by this user.
     *
     * @return The wallets.
     */
    public function wallets() {
        return $this->hasManyThrough(
            Wallet::class,
            EconomyMember::class,
            'user_id',
            'economy_member_id',
            'id',
            'id'
        );
    }

    /**
     * Get a relation to all transactions this user owns.
     *
     * @return Relation to all transactions this user owns.
     */
    public function transactions() {
        return $this->hasMany(Transaction::class, 'owner_id');
    }

    /**
     * Get the payments owned by this user.
     *
     * @return The payments.
     */
    public function payments() {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get a relation to the balance import aliases for this user.
     *
     * @return Relation to balance import aliases.
     */
    public function balanceImportAliases() {
        return $this->hasMany(BalanceImportAlias::class);
    }

    /**
     * Get the inventory changes.
     *
     * @return Inventory changes.
     */
    public function inventoryChanges() {
        return $this->hasMany(InventoryItemChange::class);
    }

    /**
     * Check whether this user has a password configured or not.
     *
     * @return boolean True if a password is configured, false if not.
     */
    public function hasPassword() {
        return !empty($this->password);
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
     * Check whether the user must validate their e-mail address now.
     *
     * @return bool True if the user should verify their e-mail address right
     * now.
     */
    public function needsToVerifyEmail() {
        return $this
            ->emails()
            ->unverified()
            ->where('created_at', '<=', now()->subSeconds(Self::REQUEST_MAIL_VERIFY_AFTER))
            ->limit(1)
            ->count() > 0;
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
     * Get the user's preferred locale.
     *
     * @return string The preferred locale.
     */
    public function preferredLocale() {
        return LangManager::getUserLocaleSafe($this);
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
            // TODO: send to all user recipients

            // Get the primary email address for the user
            $email = $this->getPrimaryEmail();

            // Send an additional reset token to allow the user to revert the password if the change was unwanted
            if($email != null) {
                // Create an additional reset token to allow the user to revert the password change
                $extraReset = PasswordResetManager::create($this);

                // Create a mailable
                // TODO: get recipient from buildEmailRecipients function
                $recipient = new EmailRecipient($email, $this);
                $mailable = new Reset($recipient, $extraReset);

                // Send the mailable
                Mail::send($mailable);
            }
        }
    }

    /**
     * Disable the password for this user.
     *
     * @param bool $sendMail True to send a mail about the password disable, false if not.
     */
    public function disablePassword($sendMail) {
        // Change the password
        $this->password = null;
        $this->save();

        // Send an email about the password change
        if($sendMail) {
            // TODO: send to all user recipients

            // Get the primary email address for the user
            $email = $this->getPrimaryEmail();

            // Send an additional reset token to allow the user to revert the password if the change was unwanted
            if($email != null) {
                // Create an additional reset token to allow the user to revert the password change
                $extraReset = PasswordResetManager::create($this);

                // Create a mailable
                // TODO: get recipient from buildEmailRecipients function
                $recipient = new EmailRecipient($email, $this);
                $mailable = new Disabled($recipient, $extraReset);

                // Send the mailable
                Mail::send($mailable);
            }
        }
    }

    /**
     * Get the primary wallet for this user in the given economy.
     * If the user has no wallet in the given community, null is returned.
     *
     * @param Economy $economy The wallet economy.
     *
     * @return Wallet|null The primary wallet, or null if there is none.
     */
    public function getPrimaryWallet(Economy $economy) {
        // TODO: put logic here to determine what the primary wallet of the user
        //       is, or is the user wallet list already sorted?

        // Get the wallet with the biggest balance
        return $economy->members()->user($user)->wallets()->first();
    }

    /**
     * Get the email recipients for this user.
     *
     * @param string [$email] Optional email address to use exclusively. If none
     *      is given, all user email addresses are returned as recipient.
     *
     * @return array An array of email recipients to send a message to.
     */
    public function buildEmailRecipients($email = null) {
        // TODO: only use verified addresses?

        // Build email recipients for all user emails
        $user = $this;
        $emails = $this
            ->emails
            ->filter(function($e) use($email) {
                // Allow all emails if none is specified
                if($email == null)
                    return true;

                // Limit to filtered email
                return $e->isEmail($email);
            })
            ->map(function($email) use($user) {
                return $email->buildEmailRecipient($user);
            });

        // Throw error if no email while filtering
        if(!empty($email) && empty($emails))
            throw new \Exception('Could not build email recipient for user, given email not linked to this user');

        return $emails;
    }
}
