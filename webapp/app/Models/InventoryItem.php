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
        return $this->hasMany(InventoryItemChange::class);
    }
}
