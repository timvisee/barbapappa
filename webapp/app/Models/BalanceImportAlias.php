<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Scopes\EnabledScope;
use App\Utils\EmailRecipient;

/**
 * Balance import alias.
 *
 * Represents an alias or user by an email address for an imported balance
 * change.
 *
 * @property int id
 * @property int economy_id
 * @property-read Economy economy
 * @property int|null user_id
 * @property-read User|null user
 * @property string|null name
 * @property string email
 * @property string name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class BalanceImportAlias extends Model {

    protected $table = 'balance_import_alias';

    protected $fillable = [
        'name',
        'email',
        'user_id',
    ];

    /**
     * Get a relation to the economy this import is part of.
     *
     * @return Relation to the economy.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * Get a relation to the user this alias is linked to.
     *
     * @return Relation to the user.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a relation to all balance import changes.
     *
     * @return Relation to balance import changes.
     */
    public function changes() {
        return $this->hasMany(BalanceImportChange::class, 'alias_id');
    }

    /**
     * Get or create a specific user alias in the given economy for the given
     * name and email address.
     *
     * If the name of the user changes over time, it is automatically updated.
     * If the alias already exists, the name is optional and may be null.
     *
     * If the alias does not exist and the name is null, no alias will be
     * created and null is returned.
     *
     * @param Economy $economy The economy the alias is in.
     * @param string|null $name The name of the user.
     * @param string $email The email address of the alias.
     * @return BalanceImportAlias|null The balance import alias.
     */
    public static function getOrCreate(Economy $economy, $name, $email) {
        // Find the alias by this email address, update it and return
        $alias = $economy
            ->balanceImportAliasses()
            ->where('email', $email)
            ->first();
        if($alias != null) {
            // Update the name if it has changed
            if(!empty($name) && $alias->name != $name) {
                $alias->name = $name;
                $alias->save();
            }

            return $alias;
        }

        // The name must be set, attempt to get through verified user email
        $user_email = Email::verified()->where('email', $email)->first();
        $user = $user_email != null ? $user_email->user : null;
        if(empty($name)) {
            if($user_email == null)
                return null;
            $name = $user->name;
        }

        // Create a new alias for this email address
        $alias = $economy->balanceImportAliasses()->create([
            'user_id' => $user != null ? $user->id : null,
            'name' => $name,
            'email' => $email,
        ]);

        // If there's are registered user, he must join economy, always link alias
        if($user != null) {
            if(!$economy->isJoined($user))
                $economy->join($user);
            $economy_member = $economy->members()->user($user)->firstOrFail();
            $economy_member->alias_id = $alias->id;
            $economy_member->save();
        } else
            $economy->members()->create([
                'alias_id' => $alias->id,
            ]);

        // TODO: create/link economy user for this alias

        return $alias;
    }
}
