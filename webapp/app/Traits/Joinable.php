<?php

namespace App\Traits;

use App\Models\User;

/**
 * A trait for models that are joinable.
 *
 * Implementing this requires a manyToMany relationship available through
 * `memberUsers()`.
 *
 * TODO: only allow implementing on Eloquent models
 * TODO: use `members()` implementation
 */
trait Joinable {

    /**
     * Let the given user join this model.
     * Note: this throws an error if the user has already joined.
     *
     * @param User $user The user to join.
     * @param int|null [$role=null] An optional role value to assign to the
     *      user.
     *
     *  @throws \Exception Throws if already joined.
     */
    public function memberJoin(User $user, $role = null) {
        // Build additional data object
        $data = [];
        if($role !== null)
            $data['role'] = $role;

        // Attach
        $this->memberUsers()->attach($user, $data);
    }

    /**
     * Let the given user leave this model.
     * Note: this throws an error if the user has not joined.
     *
     * @param User $user The user to leave.
     */
    public function memberLeave(User $user) {
        $this->memberUsers()->detach($user);
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
            ->memberUsers()
            ->limit(1)
            ->where('user_id', $user->id)
            ->count(['user_id']) == 1;
    }

    /**
     * Get a member.
     *
     * @param User $user The user to get.
     * @return Memmber instance.
     *
     * @throws \Exception Throws if there's no member for this user.
     */
    public function member(User $user) {
        return $this->members()->where('user_id', $user->id)->first();
    }
}
