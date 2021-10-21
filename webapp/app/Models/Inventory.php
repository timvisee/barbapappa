<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Inventory model.
 *
 * @property int id
 * @property string name
 * @property int economy_id
 * @property-read Economy economy
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Inventory extends Model {

    protected $table = 'inventory';

    protected $fillable = [
        'name',
        'economy_id',
    ];

    /**
     * Get the inventory economy.
     *
     * @return Economy The economy.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * Get the inventory items.
     *
     * @return List of inventory items.
     */
    public function items() {
        return $this->hasMany(InventoryItem::class);
    }

    /**
     * Get the bars that use this inventory.
     *
     * @return List of bars.
     */
    public function bars() {
        return $this->hasMany(Bar::class);
    }
}
