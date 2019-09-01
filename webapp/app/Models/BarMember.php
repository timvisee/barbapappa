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

    public $incrementing = true;

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
}
