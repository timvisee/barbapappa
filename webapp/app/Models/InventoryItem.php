<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Inventory item model.
 *
 * This represents an inventory item.
 *
 * @property int id
 * @property int inventory_id
 * @property-read Inventory inventory
 * @property int product_id
 * @property-read Product product
 * @property int quantity
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class InventoryItem extends Model {

    protected $table = 'inventory_item';

    protected $fillable = ['inventory_id', 'product_id', 'quantity'];

    /**
     * Number of seconds after which an inventory item is considered exausted
     * while its quantity remains zero.
     */
    const EXHAUSTED_AFTER = 5259600;

    /**
     * A scope to a specific product.
     */
    public function scopeProduct($query, Product $product) {
        return $query->where('product_id', $product->id);
    }

    /**
     * Get the inventory.
     *
     * @return Inventory The inventory.
     */
    public function inventory() {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Get the product.
     *
     * @return Product The product.
     */
    public function product() {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the changes.
     *
     * @return The inventory item change.
     */
    public function changes() {
        return $this->hasMany(InventoryItemChange::class, 'item_id');
    }

    /**
     * Whether this inventory item is considered exhausted.
     *
     * @return bool True if exhausted, false if not.
     */
    public function isExhausted() {
        return $this->quantity == 0
            && $this->updated_at->clone()->addSeconds(Self::EXHAUSTED_AFTER)->isPast();
    }
}
