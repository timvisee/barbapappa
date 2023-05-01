<?php

namespace App\Models;

use App\Models\Bar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use \Browser;

/**
 * Kiosk session model.
 *
 * @property int id
 * @property int bar_id
 * @property string token
 * @property int created_user_id
 * @property string created_ip
 * @property string created_user_agent
 * @property Carbon|null expire_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class KioskSession extends Model {

    protected $table = 'kiosk_session';

    protected $casts = [
        'expire_at' => 'datetime',
    ];

    /**
     * Maximum length in characters a user agent string may be.
     */
    public const USER_AGENT_MAX_LENGTH = 8192;

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
     * Get a relation to the bar this kiosk session is for.
     */
    public function bar() {
        return $this->belongsTo(Bar::class);
    }

    /**
     * Get a relation to the user that initiated this kiosk session.
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
     * Describe to the user what device this session was created on.
     *
     * @param bool [$ipFallback=true] Whether to return IP address if we cannot
     *      describe, otherwise null is returned.
     * @return string|null Session description or null.
     */
    // TODO: this is similar to Session::describe, merge
    public function describe($short = false, $ipFallback = true) {
        // Return IP if user agent is unknown
        if(empty($this->created_user_agent))
            return $ipFallback ? $this->created_ip : null;

        // Parse browser details
        $browser = Browser::parse($this->created_user_agent);

        // Describe browser
        $description = $browser->browserFamily();

        // Describe device manufacturer
        $hasBrowserFamily = $browser->deviceFamily() != null && $browser->deviceFamily() != "Unknown";
        if($hasBrowserFamily) {
            $description .= ' ' . lcfirst(__('misc.using')) . ' ' . $browser->deviceFamily();
            if(!empty($browser->deviceModel()))
                $description .= ' ' . $browser->deviceModel();
        }

        // Describe platform
        if(!$short || !$hasBrowserFamily)
            $description .= ' ' . lcfirst(__('misc.on')) . ' ' . $browser->platformName();

        // Add device class identifier
        if(!$short) {
            if($browser->isDesktop())
                $description .= ' (Desktop)';
            elseif($browser->isTablet())
                $description .= ' (Tablet)';
            elseif($browser->isMobile())
                $description .= ' (Mobile)';
        }

        return $description;
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
