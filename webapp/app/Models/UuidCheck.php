<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * UUID uniqueness checking model.
 *
 * Expired UUIDs will still be checked if they exist. The expiry time is just
 * for garbage collection.
 *
 * @property int id
 * @property string UUID
 * @property Carbon|null expire_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class UuidCheck extends Model {

    protected $table = 'uuid_check';

    protected $fillable = [
        'uuid',
        'expire_at',
    ];

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

    /**
     * Check for the given UUID.
     *
     * @param string $uuid The UUID to check.
     * @param ?Carbon $expire_at Time after which this will expire.
     * @return bool True if this UUID is unique and new, false if it already existed.
     */
    public static function checkUuidUnique(string $uuid, ?Carbon $expire_at) {
        // We must be in a database transaction
        assert_transaction();

        // Validate UUID
        if(!Str::isUuid($uuid))
            throw new \Exception("Given UUID is invalid");

        $uuid = trim($uuid);

        // UUID must be new
        $hasUuid = Self::where('uuid', $uuid)->limit(1)->count() > 0;
        if($hasUuid)
            return false;

        // Create new UUID
        Self::create([
            'uuid' => $uuid,
            'expire_at' => $expire_at,
        ]);
        return true;
    }
}
