<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Permission group model.
 *
 * @property int id
 * @property int permission_group_id
 * @property int user_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class PermissionUser extends Model {

    /**
     * A permission group user has a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user() {
        return $this->hasOne(User::class);
    }

    /**
     * A permission group user has a permission group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function group() {
        return $this->hasOne(PermissionGroup::class);
    }
}
