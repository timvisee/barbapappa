<?php

namespace App\Services\Auth;

use App\Email;
use App\Services\BarAuthManager;
use App\Session;
use App\Utils\TokenGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

/**
 * This authenticator is used to authenticate new and existing sessions.
 *
 * The current request may be authenticated through it's session token stored in a cookie.
 * Or a request may be authenticated with given user credentials.
 *
 * This class handles the authentication process and returns an authentication result.
 * The result defines whether the authentication was successful.
 * The state may then be stored somewhere, such as in the {@see BarAuthManager} class for further use.
 *
 * Class Authenticator
 * @package App\Services\Auth
 */
class Authenticator {

    /**
     * Session expiration time in seconds.
     */
    const SESSION_EXPIRE = 356 * 24 * 60 * 60;

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
        if(!Cookie::has(BarAuthManager::AUTH_COOKIE))
            return new AuthResult(AuthResult::ERR_NO_SESSION);

        // Get the session token and make sure it's valid
        $token = Cookie::get(BarAuthManager::AUTH_COOKIE);
        if($token == null)
            return new AuthResult(AuthResult::ERR_INVALID_TOKEN);

        // Authenticate the token and return the result
        return $this->authToken($token);
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
            return new AuthResult(AuthResult::ERR_INVALID_TOKEN);

        // There must be a session with this token
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $session = Session::where('token', '=', $token)->first();
        if($session == null)
            return new AuthResult(AuthResult::ERR_INVALID_TOKEN);

        // Authenticate the session and return the result
        return $this->authSession($session);
    }

    /**
     * Authenticate the given session.
     *
     * @param Session $session
     *
     * @return AuthResult The authentication result state.
     */
    public function authSession(Session $session) {
        // Make sure a session is given
        if($session == null)
            return new AuthResult(AuthResult::ERR_NO_SESSION);

        // The session must not be expired
        if($session->isExpired())
            return new AuthResult(AuthResult::ERR_EXPIRED);

        // TODO: Check whether the user has any valid email addresses!
        $emailVerified = false;

        // Return the result
        return new AuthResult(
            AuthResult::OK,
            new AuthState(
                $session,
                $emailVerified
            )
        );
    }

    /**
     * Authenticate the user with the given credentials.
     * This method will automatically set the session cookie if authentication was successful.
     *
     * @param string $email The users email address.
     * @param string $password The users password.
     *
     * @return AuthResult The authentication result state.
     */
    public function authCredentials($email, $password) {
        // The parameters must be set
        if($email == null || empty(trim($email)) || $password == null)
            return new AuthResult(AuthResult::ERR_INVALID_CREDENTIALS);

        // Trim the email address
        // TODO: Format the email address here, lowercase and strip it from dots?
        $email = trim($email);

        // Find the email addresses linked
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $emailModels = Email::where('email', '=', $email)->get();

        // Loop over the email models that have been found
        // TODO: Should we try to authenticate multiple email addresses here?
        foreach($emailModels as $emailModel) {
            // Get the user, must be valid
            $user = $emailModel->user;
            if($user == null)
                continue;

            // Get the password hash
            $hash = $user->password;

            // TODO: Validate the password here!

            // Generate an unique token and get the IP address
            $token = $this->generateUniqueToken();
            $ip = Request::ip();
            $expire = Carbon::now()->addSecond(self::SESSION_EXPIRE);

            // Create the new session object and save it
            $session = new Session();
            $session->user_id = $user->id;
            $session->token = $token;
            $session->created_ip = $ip;
            $session->expire_at = $expire;
            $session->save();

            // Set the session token cookie
            Cookie::queue('session_token', $token, self::SESSION_EXPIRE / 60);

            // TODO: Check whether this user has any verified email address
            $emailVerified = false;

            // We're authenticated now, return the state
            return new AuthResult(
                AuthResult::OK,
                new AuthState(
                    $session,
                    $emailVerified
                )
            );
        }

        // The user wasn't found, return the result
        return new AuthResult(AuthResult::ERR_INVALID_CREDENTIALS);
    }

    /**
     * Generate a new and unique session token.
     * This method blocks until a new unique token has been generated.
     *
     * @return string Unique token.
     */
    private function generateUniqueToken() {
        // Keep generating tokens until we've an unique one
        do {
            // Generate a new token
            $token = TokenGenerator::generate(self::SESSION_TOKEN_LENGTH);

            // Check whether the token exists
            /** @noinspection PhpDynamicAsStaticMethodCallInspection */
            $exists = Session::where('token', '=', $token)->first() != null;

        } while($exists);

        // Return the generated token
        return $token;
    }
}