<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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

    protected $fillable = [
        'name',
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
     * Get a relation to all balance import events that are registered for this
     * system.
     *
     * @return Relation to the events.
     */
    public function events() {
        return $this->hasMany(BalanceImportEvent::class, 'system_id');
    }

    /**
     * Get a relation to all balance import changes in this system, that are in
     * any of the system events.
     *
     * @return Relation to all changes.
     */
    public function changes() {
        return $this->hasManyThrough(
            BalanceImportChange::class,
            BalanceImportEvent::class,
            'system_id',
            'event_id',
            'id',
            'id'
        );
    }

    /**
     * Force delete this balance import system.
     * This will delete all events and changes along with it, without doing any
     * checks.
     *
     * Balance import changes that have already been committed to user wallets
     * won't be reverted.
     */
    public function forceDelete() {
        $this->changes()->delete();
        $this->delete();
    }
}
