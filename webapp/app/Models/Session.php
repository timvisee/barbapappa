<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use \Browser;

/**
 * Session model.
 *
 * @property int id
 * @property int user_id
 * @property string token
 * @property string created_ip
 * @property string created_user_agent
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
     * Check whether this session is the current session.
     * Always returns false if the session is expired.
     *
     * @return bool True if expired, false if not.
     */
    public function isCurrent() {
        // Always false if expired or not authenticated
        if($this->isExpired() || !barauth()->isAuth())
            return false;

        // Check whehter this is the current session
        $session = barauth()->getAuthState()->getSession();
        return $session != null && $session->id == $this->id;
    }

    /**
     * Check whether the current IP is the same as the session IP.
     *
     * @return bool True if IP is the same.
     */
    public function isSameIp() {
        return $this->created_ip != null
            && $this->created_ip == \Request::ip();
    }

    /**
     * Describe to the user what device this session was created on.
     *
     * @param bool [$ipFallback=true] Whether to return IP address if we cannot
     *      describe, otherwise null is returned.
     * @return string|null Session description or null.
     */
    public function describe($ipFallback = true) {
        // Return IP if user agent is unknown
        if(empty($this->created_user_agent))
            return $ipFallback ? $this->created_ip : null;

        // Parse browser details
        $browser = Browser::parse($this->created_user_agent);

        return $browser->browserName();
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
