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
 * @property-read NewCurrency currency
 * @property decimal price
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ProductPrice extends Model {

    protected $table = 'product_price';

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
     * Get a relation to the currency.
     *
     * @return Relation to the currency.
     */
    public function currency() {
        return $this->belongsTo(NewCurrency::class);
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
    // TODO: use options array instead of neutral param here
    public function formatPrice($format = BALANCE_FORMAT_PLAIN, $neutral = true) {
        return $this->currency->format($this->price, $format, [
            'neutral' => $neutral,
        ]);
    }
}
