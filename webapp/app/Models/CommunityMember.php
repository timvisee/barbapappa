<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Community member model.
 *
 * @property int id
 * @property int community_id
 * @property-read Community community
 * @property int user_id
 * @property-read user
 * @property int role
 * @property Carbon|null visited_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class CommunityMember extends Pivot {

    protected $table = 'community_member';

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
     * Scope a query to only include community members relevant to the given
     * search query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param string $search The search query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search) {
        // Don't scope if empty
        if(empty($search))
            return $query;

        // Search for each word separately in the first/last name fields
        $query = $query
            ->where(function($query) use($search) {
                foreach(explode(' ', $search) as $word)
                    if(!empty($word))
                        $query->whereExists(function($query) use($word) {
                            $query->selectRaw('1')
                                ->from('user')
                                ->whereRaw('user.id = community_member.user_id')
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
     * Get the member community.
     *
     * @return Community The community.
     */
    public function community() {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the member user.
     *
     * @return User The user.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }
}
