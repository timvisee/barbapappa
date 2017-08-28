<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Permission group selector model.
 *
 * @property int id
 * @property string name
 * @property boolean enabled
 * @property int|null community_id
 * @property int|null bar_id
 * @property int|null inherit_from
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class PermissionGroup extends Model {

    /**
     * A permission group has many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users() {
        return $this->hasMany(PermissionUser::class);
    }

    /**
     * A permission group might have many permission group user selectors.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function selectors() {
        return $this->hasMany(PermissionSelector::class);
    }

    /**
     * A permission group might have many permission entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries() {
        return $this->hasMany(PermissionEntry::class);
    }

    // TODO: Has one for community
    // TODO: Has one for bar

    /**
     * A permission group inherits one other permission group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function inherit() {
        return $this->hasOne(self::class);
    }

    /**
     * A permission group may be inherited by many other permission groups.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inherited_by() {
        return $this->hasMany(self::class);
    }

    /**
     * Check whether this group is for the application layer.
     *
     * @return bool True if for the application layer, false if not.
     */
    public function isApplicationLayer() {
        return $this->community_id == null && $this->bar_id == null;
    }

    /**
     * Check whether this group is for the community layer.
     *
     * @return bool True if for the community layer, false if not.
     */
    public function isCommunityLayer() {
        return $this->community_id != null;
    }

    /**
     * Check whether this group is for the bar layer.
     *
     * @return bool True if for the bar layer, false if not.
     */
    public function isBarLayer() {
        return $this->bar_id != null;
    }
}
