<?php

namespace App\Models;

// TODO: require Model implementation
trait Mutationable {

    /**
     * Get a relation to the mutation this belongs to.
     *
     * @return Relation to the mutation.
     */
    public function mutation() {
        return $this->morphOne(Mutation::class, 'mutationable');
    }
}
