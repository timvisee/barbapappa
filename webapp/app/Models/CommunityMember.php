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
