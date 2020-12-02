<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

// TODO: update parent mutation change time, if this model changes

/**
 * Mutation magic model.
 * This defines a generic but magic kind of mutation, that belongs to a main
 * mutation.
 *
 * @property int id
 * @property string|null description
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class MutationMagic extends Model {

    use Mutationable;

    protected $table = 'mutation_magic';

    protected $fillable = [
        'description',
    ];

    /**
     * Undo the product mutation.
     * This does not delete the mutation model.
     *
     * @throws \Exception Throws if we cannot undo right now or if not in a
     *      transaction.
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
     * Find a list of communities this mutation took part in.
     *
     * This will always be empty for this type of transaction.
     *
     * @return Collection List of communities, may be empty.
     */
    public function findCommunities() {
        return collect();
    }
}
