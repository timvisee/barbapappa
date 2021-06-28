<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Bar member model.
 *
 * @property int id
 * @property int bar_id
 * @property-read Bar bar
 * @property int user_id
 * @property-read user
 * @property int role
 * @property Carbon|null visited_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class BarMember extends Pivot {

    protected $table = 'bar_member';

    protected $with = ['user'];

    /**
     * Get dynamic properties.
     *
     * @param string $name Property name.
     *
     * @return mixed|string Result.
     */
    public function __get($name) {
        switch($name) {
            case 'name':
                return $this->user->name;
            default:
                return parent::__get($name);
        }
    }

    /**
     * Check whether dynamic properties exist.
     *
     * @param string $name Property name.
     *
     * @return bool True if exists, false if not.
     */
    public function __isset($name) {
        switch($name) {
            case 'name':
                return true;
            default:
                return parent::__isset($name);
        }
    }

    /**
     * Scope to a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param User $user The user.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUser($query, User $user) {
        return $query->where('user_id', $user->id);
    }

    /**
     * Get the member bar.
     *
     * @return Bar The bar.
     */
    public function bar() {
        return $this->belongsTo(Bar::class);
    }

    /**
     * Get the member user.
     *
     * @return User The user.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get related economy member.
     *
     * This function is expensive.
     */
    public function fetchEconomyMember() {
        // Assert user isn't null
        if($this->user_id == null)
            throw new \Exception("Cannot get economy member for bar member with no user");

        return $this
            ->bar
            ->economy
            ->members()
            ->where('user_id', $this->user_id)
            ->firstOrFail();
    }
}
