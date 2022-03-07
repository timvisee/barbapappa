<?php

namespace App\Models;

use App\Jobs\UpdateProductExhaustedEconomy;
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
     * Get all item changes within this inventory.
     *
     * @return List of item changes.
     */
    public function changes() {
        return $this->hasManyThrough(
            InventoryItemChange::class,
            InventoryItem::class,
            'inventory_id',
            'item_id',
            'id',
            'id',
        );
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
     * Get an inventory item for a given product if it exists.
     *
     * @param Product $product The product to get the inventory item for.
     * @return InventoryItem|null The inventory item if it exists.
     */
    public function getItem(Product $product): ?InventoryItem {
        return $this->items()->product($product)->first();
    }

    /**
     * Get or create the inventory item for a given product.
     *
     * @param Product $product The product to get the inventory item for.
     * @return InventoryItem The inventory item.
     */
    public function getOrCreateItem(Product $product): InventoryItem {
        // TODO: assert the product and inventory are in the same economy

        $self = $this;
        /** @var InventoryItem */
        $item = null;

        DB::transaction(function() use($self, $product, &$item) {
            $item = $self->getItem($product);
            if($item == null)
                $item = $self->items()->create([
                    'product_id' => $product->id,
                    'quantity' => 0,
                ]);
        });

        return $item;
    }

    /**
     * Add a change for a given product, setting its quantity.
     *
     * @param Product $product The product to update the quantity for.
     * @param int $type Inventory item change type.
     * @param int $quantity The quantity.
     * @param string|null $comment A user comment for this change.
     * @param User|null $user The user responsible for this change.
     * @param InventoryItemChange|null $related A related inventory item change.
     * @param MutationProduct|null $mutationProduct A related product mutation
     *      for purchase changes.
     * @return InventoryItemChange The added change.
     */
    public function setProductQuantity(
        Product $product,
        int $type,
        int $quantity,
        ?string $comment,
        ?User $user,
        ?InventoryItemChange $related,
        ?MutationProduct $mutationProduct
    ): InventoryItemChange {
        $self = $this;
        /** @var InventoryItemChange */
        $change = null;

        DB::transaction(function() use($self, $product, $type, $quantity, $comment, $user, $related, $mutationProduct, &$change) {
            $item = $self->getOrCreateItem($product);
            $change = $self->setItemQuantity($item, $type, $quantity, $comment, $user, $related, $mutationProduct);
        });

        return $change;
    }

    /**
     * Add a change for a given product, setting its quantity.
     *
     * @param InventoryItem $item The inventory item to update the quantity for.
     * @param int $type Inventory item change type.
     * @param int $quantity The quantity.
     * @param string|null $comment A user comment for this change.
     * @param User|null $user The user responsible for this change.
     * @param InventoryItemChange|null $related A related inventory item change.
     * @param MutationProduct|null $mutationProduct A related product mutation
     *      for purchase changes.
     * @return InventoryItemChange The added change.
     */
    public function setItemQuantity(
        InventoryItem $item,
        int $type,
        int $quantity,
        ?string $comment,
        ?User $user,
        ?InventoryItemChange $related,
        ?MutationProduct $mutationProduct
    ): InventoryItemChange {
        /** @var InventoryItemChange */
        $change = null;
        $self = $this;

        DB::transaction(function() use($self, $item, $type, $quantity, $comment, $user, $related, $mutationProduct, &$change) {
            $quantity = $quantity - $item->quantity;
            $change = $self->changeItem($item, $type, $quantity, $comment, $user, $related, $mutationProduct);
        });

        return $change;
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
    ): InventoryItemChange {
        $self = $this;
        /** @var InventoryItemChange */
        $change = null;

        DB::transaction(function() use($self, $product, $type, $quantity, $comment, $user, $related, $mutationProduct, &$change) {
            $item = $self->getOrCreateItem($product);
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
    ): InventoryItemChange {
        InventoryItemChange::assertValidType($type);

        /** @var InventoryItemChange */
        $change = null;

        DB::transaction(function() use($item, $type, $quantity, $comment, $user, $related, $mutationProduct, &$change) {
            // Create change
            $change = new InventoryItemChange();
            $change->item_id = $item->id;
            $change->type = $type;
            $change->quantity = $quantity;
            $change->comment = $comment;
            $change->user_id = $user?->id;
            $change->related_id = $related?->id;
            $change->mutation_product_id = $mutationProduct?->id;
            $change->save();

            // Update item quantity
            if($quantity != 0)
                $item->increment('quantity', $quantity);
        });

        return $change;
    }

    /**
     * Dispatch a job to update product exhaustion states based on the current
     * inventory state.
     *
     * This dispatches a background job, to update these states eventually.
     */
    public function dispatchUpdateProductExhausted() {
        UpdateProductExhaustedEconomy::dispatch($this->economy_id);
    }
}
