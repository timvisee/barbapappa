<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Permission group user selector model.
 *
 * @property int id
 * @property int permission_group_id
 * @property boolean|null is_authenticated
 * @property boolean|null is_verified
 * @property boolean|null is_community
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class PermissionSelector extends Model {

    /**
     * A permission group user has a permission group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function group() {
        return $this->hasOne(PermissionGroup::class);
    }
}
