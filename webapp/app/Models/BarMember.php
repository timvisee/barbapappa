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

    protected $casts = [
        'visited_at' => 'datetime',
    ];

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
     * Scope a query to only include bar members relevant to the given
     * search query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $search The search query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search) {
        // Don't scope if search is empty
        if($search === null || trim($search) === '')
            return $query;

        // Search for each word separately in the first/last name fields
        $query = $query
            ->where(function($query) use($search) {
                foreach(explode(' ', $search) as $word)
                    if(!empty($word))
                        $query->whereExists(function($query) use($word) {
                            $query->selectRaw('1')
                                ->from('user')
                                ->whereRaw('user.id = bar_member.user_id')
                                ->where(function($query) use($word) {
                                    $query->where('first_name', 'LIKE', '%' . escape_like($word) . '%')
                                        ->orWhere('last_name', 'LIKE', '%' . escape_like($word) . '%');
                                });
                        });
            });

        // // Search for each word separately in nickname field
        // foreach(explode(' ', $search) as $word)
        //     $query = $query
        //         ->orWhere('nickname', 'LIKE', '%' . escape_like($word) . '%')
        //         ->orWhere('tags', 'LIKE', '%' . escape_like($word) . '%');

        return $query;
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
     *
     * @return EconomyMember|null A community member for this member, if
     * available.
     */
    public function fetchEconomyMember() {
        // Assert user isn't null
        // TODO: should we error, even though we might return null if an economy member does not exist
        if($this->user_id == null)
            throw new \Exception("Cannot get economy member for bar member with no user");

        return $this
            ->bar
            ->economy
            ->members()
            ->where('user_id', $this->user_id)
            ->first();
    }

    /**
     * Get related community member.
     *
     * This function is expensive.
     *
     * @return CommunityMember|null A community member for this member, if
     * available.
     */
    public function fetchCommunityMember() {
        // Assert user isn't null
        // TODO: should we error, even though we might return null if an economy member does not exist
        if($this->user_id == null)
            throw new \Exception("Cannot get community member for bar member with no user");

        return $this
            ->bar
            ->community
            ->members()
            ->where('user_id', $this->user_id)
            ->first();
    }
}
