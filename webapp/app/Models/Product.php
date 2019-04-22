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
        return $this->name;
    }
}
