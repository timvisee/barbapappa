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
 * Balance import system model.
 * Ties a list of balance import events to a single external system instance.
 *
 * @property int id
 * @property int economy_id
 * @property-read Economy economy
 * @property string name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class BalanceImportSystem extends Model {

    protected $table = 'balance_import_system';

    /**
     * Get a relation to the economy this import is part of.
     *
     * @return Relation to the economy.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * Get a relation to all balance import events that are registered for this
     * system.
     *
     * @return Relation to the events.
     */
    public function events() {
        return $this->hasMany(BalanceImportEvent::class);
    }
}
