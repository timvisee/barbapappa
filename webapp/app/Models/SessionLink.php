<?php

namespace App\Models;

use App\Mail\Auth\SessionLinkMail;
use App\Utils\TokenGenerator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Session link model.
 *
 * @property int id
 * @property int user_id
 * @property-read User|null user
 * @property string token
 * @property Carbon|null expire_at
 * @property string|null intended_url
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class SessionLink extends Model {

    // TODO: use global scope to limit to non-expired tokens

    /**
     * The length in characters of reset tokens.
     * @type int
     */
    const TOKEN_LENGTH = 24;

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
     * Create a new session link for the given user.
     * If there was any intended URL in the current session, it is included in
     * the created session link to redirect the user to after authenticating.
     *
     * This will not send any mail to the user.
     *
     * @param User $user The user to create the link for.
     * @return SessionLink The session link object that was created.
     */
    public static function create(User $user) {
        // Grab the intended URL if there is any
        $intended_url = \Session::get('url.intended');
        \Session::forget('url.intended');

        // Create a session link for this user
        $link = new SessionLink();
        $link->user_id = $user->id;
        $link->token = Self::generateToken();
        $link->expire_at = now()->addSeconds(config('app.auth_session_link_expire'));
        $link->intended_url = $intended_url;
        $link->save();
        return $link;
    }

    /**
     * Generate an unique session token.
     * This method blocks until a new unique token has been generated.
     *
     * @return string Unique token.
     */
    private static function generateToken() {
        // Keep generating tokens until we've an unique one
        do {
            // Generate a new token
            $token = TokenGenerator::generate(self::TOKEN_LENGTH, false);

            // Make sure the session token is unique
            $exists = Self::where('token', '=', $token)->first() != null;

        } while($exists);

        // Return the generated token
        return $token;
    }

    /**
     * Send a mail for this session link to the corresponding user.
     * This will allow the user to authenticate by clicking the link.
     *
     * @param string [$email] An optional specific email to use, otherwise all
     *      user email addresses are used.
     */
    public function sendMail($email = null) {
        // TODO: assert the token was not consumed already
        Mail::send(new SessionLinkMail($this->user->buildEmailRecipients($email), $this));
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
        DB::transaction(function() use($link, &$authResult) {
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
