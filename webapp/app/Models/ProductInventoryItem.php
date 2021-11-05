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
        'product',
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

    /**
     * Clone this product inventory item and attach it to another product.
     *
     * @param Product $product The product to clone this to.
     * @return ProductInventoryItem The cloned item.
     *
     * @throws \Exception Throws if the other product is in a different economy,
     *      or when the given product is the same as the currently attached.
     */
    public function cloneToProduct(Product $product): ProductInventoryItem {
        // Must be in the same economy
        if($this->product->economy_id != $product->economy_id)
            throw new \Exception('Failed to clone product inventory item, other product is in a different economy');

        // Must be another product
        if($this->product->id == $product->id)
            throw new \Exception('Failed to clone product inventory item, tried to clone into the same product');

        // Clone item
        $new = new ProductInventoryItem();
        $new->product_id = $product->id;
        $new->inventory_product_id = $this->inventory_product_id;
        $new->quantity = $this->quantity;
        $new->save();

        return $new;
    }
}
