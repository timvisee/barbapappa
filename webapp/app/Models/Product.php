<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Product model.
 *
 * This represents a purchasable product.
 *
 * @property int id
 * @property int economy_id
 * @property-read Economy economy
 * @property int|null created_user_id
 * @property int|null updated_user_id
 * @property-read User|null created_user
 * @property-read User|null updated_user
 * @property int type
 * @property string name
 * @property boolean exhausted
 * @property string|null tags
 * @property Carbon|null deleted_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Product extends Model {

    use SoftDeletes;

    protected $table = 'product';

    protected $fillable = ['economy_id', 'type', 'name', 'tags', 'exhausted', 'created_user_id', 'updated_user_id'];

    protected $with = ['names'];

    protected $casts = [
        'exhausted' => 'boolean',
    ];

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
     * Get relation to user that created this product.
     *
     * @return Relation to the user that created this product.
     */
    // TODO: rename to createdUser
    public function created_user() {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    /**
     * Get relation to user that last updated this product.
     *
     * @return Relation to the user that created this product.
     */
    // TODO: rename to updatedUser
    public function updated_user() {
        return $this->belongsTo(User::class, 'updated_user_id');
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
     * Get the inventory items for this product.
     *
     * @return InventoryItem Inventory item.
     */
    public function inventoryItems() {
        return $this->hasMany(InventoryItem::class);
    }

    /**
     * Get the products to affect in the inventory when this product is bought.
     *
     * @return Product inventory items.
     */
    public function inventoryProducts() {
        return $this->hasMany(ProductInventoryItem::class);
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
            ->orWhere('tags', 'LIKE', '%' . escape_like($search) . '%')
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
     * @param [Currency]|null $currencies An ordered list of preferred currencies.
     *
     * @return ProductPrice|null The product price or null if none is found.
     */
    public function getPrice($currencies) {
        // Get the available currencies
        $prices = $this->prices;

        // If currencies is null, return first price
        if($currencies == null)
            return $prices->first();

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
        return $price->currency->format($price->price, $format, $options);
    }

    /**
     * Get a list of products with quantities to subtract from the bar inventory
     * when this is bought.
     *
     * If no alternative inventory product is selected, this simply returns the
     * current product with a quantity of 1.
     *
     * Returns the following structure:
     * [
     *     [
     *         'product' => Product,
     *         'quantity' => 1,
     *     ],
     *     [
     *         'product' => Product,
     *         'quantity' => 2,
     *     ],
     * ]
     *
     * @return array
     */
    private function inventoryProductsList() {
        // Return current if no custom is configured
        if($this->inventoryProducts->isEmpty())
            return collect([[
                'product' => $this,
                'quantity' => 1,
            ]]);

        // TODO: make this recursive! take alternative products from products configured
        // here as well

        // Build list of products
        return $this
            ->inventoryProducts
            ->map(function($p) {
                return [
                    'product' => $p->inventoryProduct,
                    'quantity' => $p->quantity,
                ];
            });
    }

    /**
     * Subtract this product from the given inventry as configured.
     *
     * This will subtract the given quantity from the inventory. If an
     * alternative list of inventroy products is configured for this product, it
     * is subtracted instead.
     *
     * @param Inventory $inventory Inventory to subtract from.
     * @param int $quantity Quantity to subtract.
     * @param MutationProduct $mutation_product Related product mutation.
     */
    public function subtractFromInventory(Inventory $inventory, int $quantity, MutationProduct $mutation_product) {
        foreach($this->inventoryProductsList() as $p) {
            // Fix for a very uncommon case where the product is null
            if($p['product'] == null)
                continue;

            // Update inventory for product
            $inventory->changeProduct(
                $p['product'],
                InventoryItemChange::TYPE_PURCHASE,
                -($p['quantity'] * $quantity),
                null,
                null,
                null,
                $mutation_product,
            );
        }
    }

    /**
     * Clone the assigned inventory products from another product, into this
     * one.
     *
     * @param Product $other The other product.
     * @param bool [$clear=true] Whether to clear/overwrite the current list of
     *      inventory products.
     *
     * @throws \Exception Throws if the other product is in a different economy.
     */
    public function cloneInventoryProductsFrom(Product $other, bool $clear = true) {
        $self = $this;
        DB::transaction(function() use(&$self, $other, $clear) {
            // Delete current
            if($clear)
                $self->inventoryProducts()->delete();

            // Clone inventory products
            $other->inventoryProducts
                ->each(function($i) use($self) {
                    $i->cloneToProduct($self);
                });
        });
    }
}
