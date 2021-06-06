<?php

namespace App\Models;

use App\Models\User;
use App\Models\Bar;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Kiosk session model.
 *
 * @property int id
 * @property int bar_id
 * @property string token
 * @property int created_user_id
 * @property string created_ip
 * @property Carbon|null expire_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class KioskSession extends Model {

    protected $table = 'kiosk_session';

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
