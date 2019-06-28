<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Session link model.
 *
 * @property int id
 * @property int user_id
 * @property-read User|null user
 * @property string token
 * @property Carbon|null expire_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class SessionLink extends Model {

    // TODO: use global scope to limit to non-expired tokens

    /**
     * A scope for session links that have not yet expired.
     *
     * @param \Builder $query The query builder.
     */
    public function scopeNotExpired($query) {
        return $query->where('expire_at', '>', now());
    }

    /**
     * Get the relation to the user this session link belongs to.
     *
     * @return Builder A relation to the user this link belongs to.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Check whether the given token is valid.
     *
     * @param bool $token The token.
     *
     * @return bool True if the token is valid, false otherwise.
     */
    public function isValidToken($token) {
        return trim($this->token) == trim($token) && !empty($this->token);
    }

    /**
     * Check whether this link has expired.
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
     * Consume the given token and authenticate.
     *
     * @param string $token The token to authenticate with.
     *
     * @return AuthResult Authentication result.
     *
     * @throws \Exception Throws if an error occurred.
     */
    public static function consume($token) {
        // The token must be valid
        if(empty(trim($token)))
            throw new \Exception('Attempting to consume session link with invalid token');

        // Find the session link
        $link = SessionLink::notExpired()
            ->where('token', trim($token))
            ->firstOrFail();

        // Create session in transaction
        $authResult = null;
        DB::transaction(function() use($link, $authResult) {
            // Get the linked user, delete session link, only allow using it once
            $user = $link->user;
            $link->delete();

            // Authenticate, create user session
            $authResult = barauth()->getAuthenticator()->createSession($user);
        });

        // TODO: move this below to controller, return auth result instead

        // Show an error if the user is not authenticated
        if($authResult->isErr())
            return redirect()
                ->back()
                ->with('error', __('general.serverError'));

        // Redirect the user to the dashboard
        return redirect()
            ->intended(route('dashboard'))
            ->with('success', __('auth.loggedIn'));
    }
}
