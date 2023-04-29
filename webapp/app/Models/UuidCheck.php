<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * UUID uniqueness checking model.
 *
 * @property int id
 * @property string UUID
 * @property Carbon|null expire_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class UuidCheck extends Model {

    protected $table = 'uuid_check';

    protected $casts = [
        'uuid' => 'uuid',
        'expire_at' => 'datetime',
    ];

    /**
     * A scope for expired UUIDs.
     *
     * @param Builder $query Query builder.
     */
    public function scopeExpired($query) {
        $query->where(function($query) {
            $query->where('expire_at', '<=', now());
        });
    }

    /**
     * Check whether this UUID has expired.
     *
     * @return bool True if expired, false if not.
     */
    public function isExpired() {
        $expireAt = $this->expire_at;
        return $expireAt != null && Carbon::parse($expireAt)->isPast();
    }
}
