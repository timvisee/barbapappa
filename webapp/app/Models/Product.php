<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

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
 * @property bool archived
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Product extends Model {

    protected $table = "products";

    protected $fillable = [
        'economy_id',
        'type',
        'name',
        'enabled',
        'archived',
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
}
