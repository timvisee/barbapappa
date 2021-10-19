<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Product name model.
 *
 * This represents a localized name for a purchasable product.
 *
 * @property int id
 * @property int product_id
 * @property-read Product product
 * @property string locale
 * @property string name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class ProductName extends Model {

    protected $table = 'product_name';

    protected $fillable = [
        'product_id',
        'locale',
        'name',
    ];

    protected $touches = [
        'product'
    ];

    /**
     * Get the relation to the product this localized name belongs to.
     *
     * @return Relation to the product this localized name belongs to.
     */
    public function product() {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the display name of the language corresponding to the name locale.
     *
     * @return string The language name.
     */
    public function languageName() {
        return __('lang.name', [], $this->locale);
    }

    /**
     * Check whether the language this transalation is in is hidden for the
     * current user.
     *
     * @return bool True if hidden, false if not.
     */
    public function isHiddenLanguage() {
        return langManager()->isHiddenLocale($this->locale);
    }
}
