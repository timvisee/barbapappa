<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Inventory model.
 *
 * @property int id
 * @property string name
 * @property int economy_id
 * @property-read Economy economy
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Inventory extends Model {

    protected $table = 'inventory';

    protected $fillable = [
        'name',
        'economy_id',
    ];

    /**
     * Get the inventory economy.
     *
     * @return Economy The economy.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * Get the inventory items.
     *
     * @return List of inventory items.
     */
    public function items() {
        return $this->hasMany(InventoryItem::class);
    }

    /**
     * Get the bars that use this inventory.
     *
     * @return List of bars.
     */
    public function bars() {
        return $this->hasMany(Bar::class);
    }

    /**
     * Add a change for a given product.
     *
     * A positive quantity means the amount is added to the inventory.
     *
     * @param Product $product The product to update the quantity for.
     * @param int $type Inventory item change type.
     * @param int $quantity The change amount.
     * @param string|null $comment A user comment for this change.
     * @param User|null $user The user responsible for this change.
     * @param InventoryItemChange|null $related A related inventory item change.
     * @param MutationProduct|null $mutationProduct A related product mutation
     *      for purchase changes.
     * @return InventoryItemChange The added change.
     */
    public function changeProduct(
        Product $product,
        int $type,
        int $quantity,
        ?string $comment,
        ?User $user,
        ?InventoryItemChange $related,
        ?MutationProduct $mutationProduct
    ) {
        $self = $this;
        $change = null;
        DB::transaction(function() use($self, $product, $type, $quantity, $comment, $user, $related, $mutationProduct, &$change) {
            // Get or create inventory item
            $item = $self->items()->product($product)->first();
            if($item == null)
                $item = $self->items()->create([
                    'product_id' => $product->id,
                    'quantity' => 0,
                ]);

            // Change the item
            $change = $self->changeItem($item, $type, $quantity, $comment, $user, $related, $mutationProduct);
        });

        return $change;
    }

    /**
     * Add a change for a given inventory item.
     *
     * A positive quantity means the amount is added to the inventory.
     *
     * @param InventoryItem $item The inventory item to update the quantity for.
     * @param int $type Inventory item change type.
     * @param int $quantity The change amount.
     * @param string|null $comment A user comment for this change.
     * @param User|null $user The user responsible for this change.
     * @param InventoryItemChange|null $related A related inventory item change.
     * @param MutationProduct|null $mutationProduct A related product mutation
     *      for purchase changes.
     * @return InventoryItemChange The added change.
     */
    public function changeItem(
        InventoryItem $item,
        int $type,
        int $quantity,
        ?string $comment,
        ?User $user,
        ?InventoryItemChange $related,
        ?MutationProduct $mutationProduct
    ) {
        InventoryItemChange::assertValidType($type);

        $change = null;
        DB::transaction(function() use($item, $type, $quantity, $comment, $user, $related, $mutationProduct, &$change) {
            // Create change
            $change = new InventoryItemChange();
            $change->item_id = $item->id;
            $change->type = $type;
            $change->quantity = $quantity;
            $change->comment = $comment;
            $change->user_id = $user != null ? $user->id : null;
            $change->related_id = $related != null ? $related->id : null;
            $change->mutation_product_id = $mutationProduct != null ? $mutationProduct->id : null;
            $change->save();

            // Update item quantity
            $item->quantity += $quantity;
            $item->save();
        });

        return $change;
    }
}
