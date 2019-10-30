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
}
