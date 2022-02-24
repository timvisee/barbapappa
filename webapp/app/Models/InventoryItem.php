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
     * Number of seconds in a week.
     */
    const WEEK_SECONDS = 7 * 24 * 60 * 60;

    /**
     * Number of seconds in a month.
     */
    const MONTH_SECONDS = 2629800;

    /**
     * Number of seconds after which an inventory item is considered exausted
     * while its quantity remains zero.
     */
    const EXHAUSTED_AFTER = Self::WEEK_SECONDS;

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
     * @param bool $deep=false True to do a deep check, gives more reliable
     *      results but is expensive.
     *
     * @return bool True if exhausted, false if not.
     */
    public function isExhausted(bool $deep = false) {
        // Never exhausted if there is a non-zero quantity
        if($this->quantity != 0)
            return false;

        // Determine whether the last update time is considered exhausted
        $last_update_exhausted = $this->updated_at->clone()->addSeconds(Self::EXHAUSTED_AFTER)->isPast();

        // Deep checks
        if($deep) {
            // If product has no changes, and last update is within short
            // time, consider it not exhausted
            if($this->changes()->limit(1)->count() == 0 && !$last_update_exhausted)
                return false;

            // If last change is manual by admin, it is now exhausted
            if($this->changes()->latest()->first()?->isManual() ?? false)
                return true;
        }

        return $last_update_exhausted;
    }

    /**
     * Estimate the monthly purchase volume for this product from its inventroy.
     *
     * @return int Estimated monthly purchases.
     */
    public function estimateMonthlyPurchaseVolume(): int {
        // Get oldest change in wider period to determine usable period in seconds
        // Choose period between [1 hour, 1 month]
        $oldest = $this
            ->changes()
            ->period(now()->subMonths(2), null)
            ->reorder()
            ->oldest()
            ->first();
        if($oldest == null)
            return 0;
        $secs = min(max($oldest->created_at->diffInSeconds(), 60 * 60), Self::MONTH_SECONDS);

        // Get volume within a period
        $volume = abs($this
            ->changes()
            ->period(now()->subMonth(), null)
            ->type(InventoryItemChange::TYPE_PURCHASE)
            ->sum('quantity'));

        // Extrapolate to a month
        return ceil($volume / $secs * Self::MONTH_SECONDS);
    }
}
