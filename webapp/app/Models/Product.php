<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// TODO: enable EnabledScope by default

/**
 * Product model.
 *
 * This represents a purchasable product.
 *
 * @property int id
 * @property int economy_id
 * @property-read Economy economy
 * @property int|null user_id
 * @property-read User|null user
 * @property int type
 * @property string name
 * @property bool enabled
 * @property Carbon|null deleted_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Product extends Model {

    use SoftDeletes;

    protected $table = 'product';

    protected $fillable = ['economy_id', 'type', 'name', 'enabled'];

    protected $with = ['names'];

    /**
     * Normal persistent product type.
     */
    const TYPE_NORMAL = 1;

    /**
     * Custom single use product by some user.
     */
    const TYPE_CUSTOM = 2;

    /**
     * Get the relation to the economy this product is part of.
     *
     * @return Relation to the economy this product is part of.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * Get a relation to the user that added this product.
     *
     * @return Relation to the user that added this product.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a relation to the configured localized names for this product.
     *
     * @return Relation to the configured localized names for this product.
     */
    public function names() {
        return $this->hasMany(ProductName::class);
    }

    /**
     * Get a relation to the configured prices for this product.
     *
     * @return Relation to the configured prices for this product.
     */
    public function prices() {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * Scope a query to only include products relevant to the given search
     * query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $search The search query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search) {
        return $query
            ->where('name', 'LIKE', '%' . escape_like($search) . '%')
            ->orWhereExists(function($query) use($search) {
                $query->selectRaw('1')
                    ->from('product_name')
                    ->whereRaw('product.id = product_name.product_id')
                    ->where('name', 'LIKE', '%' . escape_like($search) . '%');
            });
    }

    /**
     * Scope a query to only include products having a price in any of the given
     * currencies.
     *
     * The currencies must be a single, or a list of Currency IDs.
     * If null is given, this scope will not filter.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param [int]|null $currency_ids A list of `Currency` IDs.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHavingCurrency($query, $currency_ids) {
        // Do not filter if null
        if($currency_ids === null)
            return $query;

        // Filter, make sure the product has any of the currency prices set
        return $query
            ->whereExists(function($query) use($currency_ids) {
                $query->selectRaw('1')
                    ->from('product_price')
                    ->whereRaw('product.id = product_price.product_id')
                    ->whereIn('currency_id', $currency_ids);
            });
    }

    /**
     * Get the display name for this product.
     *
     * @return Product display name.
     */
    public function displayName() {
        return $this
            ->names
            ->whereStrict('locale', langManager()->getLocaleSafe())
            ->map(function($n) { return $n->name; })
            ->first()
            ?? $this->name;
    }

    /**
     * Get the display name for the product type.
     *
     * @return Type display name.
     */
    public function typeName() {
        // Get the type key here
        $key = [
            Self::TYPE_NORMAL => 'normal',
            Self::TYPE_CUSTOM => 'custom',
        ][$this->type];
        if(empty($key))
            throw new \Exception("Unknown product type, cannot get type name");

        // Translate and return
        return __('pages.products.type.' . $key);
    }

    // TODO: define ordered list of currencies for the user

    /**
     * Get a price to show to the user for a product.
     * This method takes an ordered list of possible currencies into account.
     * The first currency in the list is preferred.
     * If the product does not have any of the preferred currencies, null is
     * returned.
     *
     * TODO: should we use currency IDs instead
     * @param [Currency] $currencies An ordered list of preferred currencies.
     *
     * @return ProductPrice|null The product price or null if none is found.
     */
    public function getPrice($currencies) {
        // Get the available currencies
        $prices = $this->prices;

        // Try to find a matching price currency
        foreach($currencies as $currency) {
            // Find a price with a matching currency
            $price = $prices
                ->whereStrict('currency_id', $currency->id)
                ->first();

            // Return the price if one is found
            if($price != null)
                return $price;
        }

        // Nothing was found
        return null;
    }

    /**
     * Format the price for this product as a human readable text using the
     * proper currency format.
     *
     * The price used is based on the `getPrice()` method. Therefore an ordered
     * list of preferred currencies must be given.
     *
     * TODO: should we use currency IDs instead
     * @param [Currency] $currencies An ordered list of preferred currencies.
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     * @param array [$options=[]] A list of formatting options.
     *
     * @return string|null Formatted price or null if no matching price is
     *      found.
     */
    public function formatPrice($currencies, $format = BALANCE_FORMAT_PLAIN, $options = []) {
        // Obtain the price
        $price = $this->getPrice($currencies);
        if($price == null)
            return null;

        // Render the price and return
        return balance($price->price, $price->currency->code, $format, $options);
    }
}
