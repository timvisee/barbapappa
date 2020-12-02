<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

// TODO: update parent mutation change time, if this model changes

/**
 * Mutation product model.
 * This defines additional information for a product mutation, that belongs to a
 * main mutation.
 *
 * @property int id
 * @property int|null product_id
 * @property int|null bar_id
 * @property int quantity
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class MutationProduct extends Model {

    use Mutationable;

    protected $table = 'mutation_product';

    protected $with = ['product'];

    protected $fillable = [
        'product_id',
        'bar_id',
        'quantity',
    ];

    /**
     * Get the product this mutation had an effect on.
     *
     * @return The affected product.
     */
    public function product() {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the bar the product for this mutation was bought at.
     *
     * @return The bar the product was bought at.
     */
    public function bar() {
        return $this->belongsTo(Bar::class);
    }

    /**
     * Undo the product mutation.
     * This does not delete the mutation model.
     *
     * @throws \Exception Throws if we cannot undo right now.
     */
    public function undo() {}

    /**
     * Handle changes as effect of a state change.
     * This method is called when the state of the mutation changes.
     *
     * For a wallet mutation, this method would change the wallet balance when
     * the new state defines success.
     *
     * @param Mutation $mutation The mutation, parent of this instance.
     * @param int $oldState The old state.
     * @param int $newState The new, current state.
     */
    public function applyState(Mutation $mutation, int $oldState, int $newState) {}

    /**
     * Find a list of communities this transaction took part in.
     *
     * This will return the community the product is in if known.
     * It will attempt to find the community by the linked bar otherwise.
     *
     * @return Collection List of communities, may be empty.
     */
    public function findCommunities() {
        if($this->product != null)
            return collect([$this->product->economy->community]);
        if($this->bar != null)
            return collect([$this->bar->community]);
        return collect();
    }

    /**
     * Get a list of all relevant and related objects to this mutation.
     * Can be used to generate a list of links on a mutation inspection page, to
     * the respective objects.
     *
     * This will return related products.
     *
     * This is an expensive function.
     *
     * @return Collection List of objects.
     */
    public function getRelatedObjects() {
        if($this->product_id != null)
            return [$this];
        return [];
    }
}
