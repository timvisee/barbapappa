<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

// TODO: update parent mutation change time, if this model changes

/**
 * Mutation balance import model.
 * This defines a mutation for an applied balance import change, that belongs
 * to a main mutation.
 *
 * @property int id
 * @property-read int|null balance_import_change
 * @property int|null balance_import_change_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class MutationBalanceImport extends Model {

    use Mutationable;

    protected $table = 'mutation_balance_import';

    protected $fillable = [
        'balance_import_change_id',
    ];

    /**
     * Get the balance import change this mutation had an effect on.
     *
     * @return The affected wallet.
     */
    public function balanceImportChange() {
        return $this->belongsTo(BalanceImportChange::class);
    }

    /**
     * Undo the balance import mutation.
     * This does not delete the mutation model.
     *
     * @throws \Exception Throws if we cannot undo right now or if not in a
     *      transaction.
     */
    public function undo() {
        // Explicitly unset balance import change commit date
        if($this->balanceImportChange != null)
            $this->balanceImportChange->update([
                'committed_at' => null,
            ]);
    }

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
     * Find a list of communities this mutation took part in.
     *
     * This will always be empty for this type of transaction.
     *
     * @return Collection List of communities, may be empty.
     */
    public function findCommunities() {
        if($this->balanceImportChange != null)
            return collect([$this->balanceImportChange->event->system->economy->community]);
        return collect();
    }

    /**
     * Get a list of all relevant and related objects to this mutation.
     * Can be used to generate a list of links on a mutation inspection page, to
     * the respective objects.
     *
     * This will return the related balance import.
     *
     * This is an expensive function.
     *
     * @return Collection List of objects.
     */
    public function getRelatedObjects() {
        if($this->balance_import_change_id != null)
            return [$this->balanceImportChange];
        return [];
    }
}
