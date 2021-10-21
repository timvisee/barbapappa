<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Product inventory item model.
 *
 * This represents the inventory product to adjust the inventory quantity for
 * when buying a product.
 *
 * @property int id
 * @property int product_id
 * @property-read Product product
 * @property int inventory_product_id
 * @property-read Product inventoryProduct
 * @property int quantity
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ProductInventoryItem extends Model {

    protected $table = 'product_inventory_item';

    protected $fillable = [
        'product_id',
        'inventory_product_id',
        'quantity',
    ];

    protected $touches = [
        'product'
    ];

    /**
     * Get the product this is for.
     *
     * @return Product The product.
     */
    public function product() {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the inventory product to adjust.
     *
     * @return Product The product.
     */
    public function inventoryProduct() {
        return $this->belongsTo(Product::class, 'inventory_product_id');
    }
}
