<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Session model.
 *
 * @property int id
 * @property int user_id
 * @property string token
 * @property string created_ip
 * @property Carbon expire_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Session extends Model {

    protected $table = 'session';

    protected $casts = [
        'expire_at' => 'datetime',
    ];

    /**
     * A scope for active sessions.
     *
     * @param Builder $query Query builder.
     */
    public function scopeActive($query) {
        $query->whereNotNull('expire_at')
            ->where('expire_at', '>', now());
    }

    /**
     * A scope for expired sessions.
     *
     * @param Builder $query Query builder.
     */
    public function scopeExpired($query) {
        $query->where(function($query) {
            $query->whereNull('expire_at')
                ->orWhere('expire_at', '<=', now());
        });
    }

    /**
     * The user this session belongs to.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Check whether this session has expired.
     *
     * @return bool True if expired, false if not.
     */
    public function isExpired() {
        // Get the attribute value, and make sure it's valid
        $expireAt = $this->expire_at;
        if($expireAt == null)
            return true;

        // Check whether the time is expired
        return Carbon::parse($expireAt)->isPast();
    }

    /**
     * Invalidate this session.
     * This makes the session expire from this moment, so it can't be used anymore for authentication.
     * If the session has already expired, nothing is changed.
     */
    public function invalidate() {
        // Return if the session has already expired
        if($this->isExpired())
            return;

        // Invalidate the session now
        $this->expire_at = Carbon::now();
        $this->save();
    }
}
