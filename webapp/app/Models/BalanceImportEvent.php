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
 * Balance import event model.
 * Groups a list of balance import changes defining an import event.
 *
 * @property int id
 * @property int system_id
 * @property-read BalanceImportSystem system
 * @property string name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class BalanceImportEvent extends Model {

    protected $table = 'balance_import_event';

    protected $fillable = [
        'name',
    ];

    /**
     * Get a relation to the system this import is part of.
     *
     * @return Relation to the system.
     */
    public function system() {
        return $this->belongsTo(BalanceImportSystem::class, 'system_id');
    }

    /**
     * Get a relation to all balance import changes.
     *
     * @return Relation to balance import changes.
     */
    public function changes() {
        return $this->hasMany(BalanceImportChange::class, 'event_id');
    }
}
