<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Product price model.
 *
 * This represents a price of a purchasable product.
 *
 * @property int id
 * @property int product_id
 * @property-read Product product
 * @property int currency_id
 * @property-read EconomyCurrency currency
 * @property decimal price
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ProductPrice extends Model {

    protected $table = "product_prices";

    protected $fillable = [
        'product_id',
        'currency_id',
        'price',
    ];

    /**
     * Get the relation to the product this price belongs to.
     *
     * @return Relation to the product this price belongs to.
     */
    public function product() {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the relation to the economy currency this price belongs to.
     *
     * @return Relation to the economy currency this price belongs to.
     */
    public function currency() {
        return $this->belongsTo(EconomyCurrency::class);
    }

    /**
     * Format the price for this product as a human readable text using the
     * proper currency format.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     * @param boolean [$neutral=true] True to neutrally format.
     *
     * @return string Formatted price.
     */
    public function formatPrice($format = BALANCE_FORMAT_PLAIN, $neutral = true) {
        // TODO: optimize this currency->currency chain
        return balance($this->price, $this->currency->currency->code, $format, null, $neutral);
    }
}
