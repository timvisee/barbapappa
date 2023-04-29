<?php

namespace App\Models;

use Carbon\Carbon;
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
     * Check if the given UUID is registered.
     *
     * @param string $uuid The UUID to check.
     * @return bool True if this UUID is registered, false if not.
     */
    public static function hasUuid(string $uuid) {
        // Validate UUID
        if(!Str::isUuid($uuid))
            throw new \Exception("Given UUID is invalid");

        return Self::where('uuid', trim($uuid))->limit(1)->count() > 0;
    }

    /**
     * Cliam the given UUID, and make sure it is unique.
     *
     * @param string $uuid The UUID to claim.
     * @param ?Carbon $expire_at Time after which this will expire.
     * @param bool $throwException=true True to throw exception if UUID is not unique.
     * @return bool True if UUID is claimed, false if not because it already existed.
     */
    public static function claim(string $uuid, ?Carbon $expire_at, bool $throwException = true) {
        // We must be in a database transaction
        assert_transaction();

        // Validate UUID
        if(!Str::isUuid($uuid))
            throw new \Exception("Given UUID is invalid");

        $uuid = trim($uuid);

        // UUID must be new
        $hasUuid = Self::where('uuid', $uuid)->limit(1)->count() > 0;
        if($hasUuid) {
            if($throwException)
                throw new \Exception("Failed to claim UUID, it was already claimed");
            return false;
        }

        // Create new UUID
        Self::create([
            'uuid' => $uuid,
            'expire_at' => $expire_at,
        ]);
        return true;
    }
}
