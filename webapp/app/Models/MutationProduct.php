<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Scopes\EnabledScope;
use App\Utils\EmailRecipient;

// TODO: update parent mutation change time, if this model changes

/**
 * Mutation product model.
 * This defines additional information for a product mutation, that belongs to a
 * main mutation.
 *
 * @property int id
 * @property int mutation_id
 * @property int|null product_id
 * @property int|null bar_id
 * @property int quantity
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class MutationProduct extends Model {

    protected $table = "mutations_product";

    /**
     * Get the main mutation this product mutation data belongs to.
     *
     * @return The main mutation.
     */
    public function mutation() {
        return $this->belongsTo('App\Models\Mutation');
    }

    /**
     * Get the product this mutation had an effect on.
     *
     * @return The affected product.
     */
    public function product() {
        return $this->belongsTo('App\Models\Product');
    }

    /**
     * Get the bar the product for this mutation was bought at.
     *
     * @return The bar the product was bought at.
     */
    public function bar() {
        return $this->belongsTo('App\Models\Bar');
    }
}
