<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Inventory item change model.
 *
 * This represents an inventory item change.
 *
 * @property int id
 * @property int item_id
 * @property-read InventoryItem item
 * @property int type
 * @property int quantity
 * @property int|null related_id
 * @property-read ModelsInventoryItemChange|null related
 * @property int|null user_id
 * @property-read User|null user
 * @property string|null comment
 * @property int|null mutation_product_id
 * @property-read MutationProduct|null mutation_product
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class InventoryItemChange extends Model {

    protected $table = 'inventory_item_change';

    protected $fillable = [
        'item_id',
        'type',
        'quantity',
        'related_id',
        'user_id',
        'comment',
        'mutation_product_id',
    ];

    /**
     * Change type: update by user, to balance inventory.
     */
    const TYPE_UPDATE = 1;

    /**
     * Change type: move to/from other inventory by user.
     */
    const TYPE_MOVE = 2;

    /**
     * Change type: product purchase.
     */
    const TYPE_PURCHASE = 3;

    /**
     * A scope to changes by a specific user.
     *
     * @param User|int $user_id The user.
     */
    public function scopeUser($query, $user_id) {
        if($user_id instanceof User)
            $user_id = $user_id->id;
        if($user_id == null)
            throw new \Exception("User cannot be null");
        return $query->where('user_id', $user_id);
    }

    /**
     * A scope to changes for a given product mutation.
     *
     * @param MutationProduct|int $mutation_product_id The product mutation.
     */
    public function scopeMutationProduct($query, $mutation_product_id) {
        if($mutation_product_id instanceof MutationProduct)
            $mutation_product_id = $mutation_product_id->id;
        if($mutation_product_id == null)
            throw new \Exception("MutationProduct cannot be null");
        return $query->where('mutation_product_id', $mutation_product_id);
    }

    /**
     * A scope to a specific change type.
     */
    public function scopeType($query, int $type) {
        return $query->where('type', $type);
    }

    /**
     * Get the item.
     *
     * @return Item The item.
     */
    public function item() {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    /**
     * Get the related change.
     *
     * Note: this only returns a change that is set as related on the current
     * change. This doesn't include reverse related changes.
     *
     * @return InventoryItemChange|null Inventory item change if set.
     */
    public function related() {
        return $this->belongsTo(InventoryItemChange::class, 'related_id');
    }

    /**
     * Get the user if set.
     *
     * @return Item The user if set.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the mutation product if set.
     *
     * @return Item The mutation product if set.
     */
    public function mutation_product() {
        return $this->belongsTo(MutationProduct::class);
    }

    /**
     * Check if a given type is valid.
     *
     * @param int $type The type to check.
     * @return bool True if valid, false if not.
     */
    public static function isValidType(int $type): bool {
        switch($type) {
        case Self::TYPE_UPDATE:
        case Self::TYPE_MOVE:
        case Self::TYPE_PURCHASE:
            return true;

        default:
            return false;
        }
    }

    /**
     * Assert the given type is valid.
     *
     * @param int $type The type to assert.
     */
    public static function assertValidType(int $type) {
        if(!Self::isValidType($type))
            throw new \Exception("Invalid inventory change type: " . $type);
    }

    /**
     * Undo and delete this change.
     *
     * This will revert the inventory quantity change.
     */
    public function undo() {
        $self = $this;
        DB::transaction(function() use($self) {
            // Revert item quantity
            $self->item->decrement('quantity', $self->quantity);

            // Delete this change
            $self->delete();
        });
    }
}