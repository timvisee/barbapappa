<?php

namespace App\Services\Auth\Kiosk;

use App\Models\Bar;
use App\Models\KioskSession;
use App\Models\User;
use App\Utils\TokenGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Sentry;

/**
 * Class Authenticator.
 *
 * This authenticator is used to authenticate new and existing sessions for
 * kiosks.
 *
 * The current request may be authenticated through it's session token stored in a cookie.
 * Or a request may be authenticated with given user credentials.
 *
 * This class handles the authentication process and returns an authentication result.
 * The result defines whether the authentication was successful.
 * The state may then be stored somewhere, such as in the {@see BarAuthManager} class for further use.
 *
 * @package App\Services\Auth\Kiosk
 */
class Authenticator {

    /**
     * The name of the authentication token cookie.
     */
    const AUTH_COOKIE = 'session_token_kiosk';

    /**
     * Session expiration time in seconds.
     */
    const SESSION_EXPIRE = 5 * 356 * 24 * 60 * 60;

    /**
     * Length in characters of the session token.
     */
    const SESSION_TOKEN_LENGTH = 64;

    /**
     * Authenticate the current request.
     *
     * @return AuthResult The authentication result state.
     */
    public function authRequest() {
        // Make sure a session token cookie is set
        if(!Cookie::has(self::AUTH_COOKIE))
            return self::finalizeResult(AuthResult::ERR_NO_SESSION);

        // Get the token and authenticate it
        return $this->authToken(
            Cookie::get(self::AUTH_COOKIE)
        );
    }

    /**
     * Authenticate the given token.
     *
     * @param string $token The token to authenticate.
     *
     * @return AuthResult The authentication result state.
     */
    public function authToken($token) {
        // The token may not be null
        if($token == null)
            return self::finalizeResult(AuthResult::ERR_INVALID_TOKEN);

        // Get the corresponding session and make sure it's valid
        $session = KioskSession::where('token', '=', $token)->first();
        if($session == null)
            return self::finalizeResult(AuthResult::ERR_INVALID_TOKEN);

        // Authenticate the session and return the result
        return $this->authSession($session);
    }

    /**
     * Authenticate the given kiosk session.
     *
     * @param KioskSession $session
     *
     * @return AuthResult The authentication result state.
     */
    public function authSession(KioskSession $session) {
        // Make sure a session is given
        if($session == null)
            return self::finalizeResult(AuthResult::ERR_NO_SESSION);

        // The session must not be expired
        if($session->isExpired())
            return self::finalizeResult(AuthResult::ERR_EXPIRED);

        // Return the result
        return self::finalizeResult(
            AuthResult::OK,
            new AuthState($session)
        );
    }

    /**
     * Create a kiosk session for the given bar, initiated by the given user.
     *
     * @param Bar $bar Bar to create kiosk session for.
     * @param User $user User that initiated the session creation.
     *
     * @return AuthResult Authentication result.
     */
    public function createSession(Bar $bar, User $user) {
        // TODO: enforce that the user has proper permissions

        // Make sure the bar and user user are valid
        if($bar == null || $user == null)
            return self::finalizeResult(AuthResult::ERR_NO_SESSION);

        // Generate an unique token and get the IP address
        $token = self::generateUniqueToken();
        $ip = Request::ip();
        $userAgent = Request::userAgent();
        $expire = Carbon::now()->addSecond(self::SESSION_EXPIRE);

        // Create the new kiosk session object and save it
        $session = new KioskSession();
        $session->bar_id = $bar->id;
        $session->user_id = $user->id;
        $session->token = $token;
        $session->created_ip = $ip;
        $session->expire_at = $expire;
        $session->save();

        // We're authenticated now, return the state
        return self::finalizeResult(
            AuthResult::OK,
            new AuthState($session)
        );
    }

    /**
     * Generate a new and unique session token.
     * This method blocks until a new unique token has been generated.
     * @return string Unique token.
     */
    private static function generateUniqueToken() {
        // Keep generating tokens until we've an unique one
        do {
            // Generate a new token
            $token = TokenGenerator::generate(self::SESSION_TOKEN_LENGTH, true);

            // Check whether the token exists
            $exists = KioskSession::where('token', '=', $token)->first() != null;

        } while($exists);

        // Return the generated token
        return $token;
    }

    /**
     * A helper function used to finalize authentication and create a result object.
     * This helper function also creates or forgets session cookies when required.
     *
     * @param int $result The result code to return with, any result constant of {@see AuthResult}.
     * @param AuthState|null $authState =null Authentication state on success.
     * @return AuthResult Authentication result object.
     */
    private static function finalizeResult($result, AuthState $authState = null) {
        // Define the authentication result
        $authResult = new AuthResult($result, $authState);

        // Set a cookie if this is a new session
        if($authResult->isOk() && $authState != null && !Cookie::has(self::AUTH_COOKIE))
            Cookie::queue(
                Cookie::make(
                    self::AUTH_COOKIE,
                    $authState->getSession()->token,
                    self::SESSION_EXPIRE / 60
                )
            );

        // Forget the session cookie if the session became invalid
        else if($authResult->isErr() && $authResult->getResult() != AuthResult::ERR_NO_SESSION)
            Cookie::queue(
                Cookie::forget(self::AUTH_COOKIE)
            );

        // Annotate authentication details for Sentry error reporting
        Sentry\configureScope(function(Sentry\State\Scope $scope) use($authResult, $authState): void {
            // Set authentication type
            $isAuth = $authResult->isOk();
            $scope->setTag("auth.type", $isAuth != null ? "kiosk" : "none");

            // Annotate current session user
            if($isAuth) {
                $bar_id = $authState->getBar()->id ?? null;
                $scope->setTag("auth.bar_id", $bar_id);
                $scope->setTag("auth.kiosk_session_id", $authState->getSession()->id);
                $scope->setUser([
                    'id' => $bar_id,
                    'ip' => Request::ip(),
                    'name' => $authState->getBar()->name ?? 'Kiosk',
                ]);
            }
        });

        return $authResult;
    }
}
