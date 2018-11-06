<?php

namespace App\Traits;

use App\Models\User;

/**
 * A trait for models that are joinable.
 *
 * Implementing this requires a manyToMany relationship available through
 * `users()`.
 *
 * TODO: force that this is implemented on Eloquent models
 */
trait Joinable {

    /**
     * Let the given user join this model.
     * Note: this throws an error if the user has already joined.
     *
     * @param User $user The user to join.
     */
    public function join(User $user) {
        $this->users()->attach($user);
    }

    /**
     * Let the given user leave this model.
     * Note: this throws an error if the user has not joined.
     *
     * @param User $user The user to leave.
     */
    public function leave(User $user) {
        $this->users()->detach($user);
    }

    /**
     * Check whether the given user is joined this model.
     *
     * @param User $user The user to check for.
     *
     * @return boolean True if joined, false if not.
     */
    public function isJoined(User $user) {
        // Optimized query
        return $this
            ->users()
            ->limit(1)
            ->where('user_id', $user->id)
            ->count(['user_id']) == 1;
    }
}
