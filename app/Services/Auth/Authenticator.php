<?php

namespace App\Services\Auth;

use App\Email;
use App\Services\BarAuthManager;
use App\Session;
use App\User;
use App\Utils\TokenGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
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
     * The name of the authentication token cookie.
     */
    const AUTH_COOKIE = 'session_token';

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
        if(!Cookie::has(self::AUTH_COOKIE))
            return $this->finalizeResult(AuthResult::ERR_NO_SESSION);

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
            return $this->finalizeResult(AuthResult::ERR_INVALID_TOKEN);

        // Get the corresponding session and make sure it's valid
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $session = Session::where('token', '=', $token)->first();
        if($session == null)
            return $this->finalizeResult(AuthResult::ERR_INVALID_TOKEN);

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
            return $this->finalizeResult(AuthResult::ERR_NO_SESSION);

        // The session must not be expired
        if($session->isExpired())
            return $this->finalizeResult(AuthResult::ERR_EXPIRED);

        // Return the result
        return $this->finalizeResult(
            AuthResult::OK,
            new AuthState($session)
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
            return $this->finalizeResult(AuthResult::ERR_INVALID_CREDENTIALS);

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

            // The password must be valid
            if(!Hash::check($password, $hash))
                continue;

            // Rehash the password if needed
            if(Hash::needsRehash($hash)) {
                // Log a message
                Log::info('Rehashing password for user with ID ' . $user->id);

                // Rehash and save the password
                $user->password = Hash::make($password);
                $user->save();
            }

            // Create a session for this user, return the result
            return $this->createSession($user);
        }

        // The user wasn't found, return the result
        return $this->finalizeResult(AuthResult::ERR_INVALID_CREDENTIALS);
    }

    /**
     * Create a session for the given user.
     *
     * @param User $user User to create the session for.
     *
     * @return AuthResult Authentication result.
     */
    public function createSession(User $user) {
        // Make sure the user is valid
        if($user == null)
            return $this->finalizeResult(AuthResult::ERR_NO_SESSION);

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

        // We're authenticated now, return the state
        return $this->finalizeResult(
            AuthResult::OK,
            new AuthState($session)
        );
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

    /**
     * A helper function used to finalize authentication and create a result object.
     * This helper function also creates or forgets session cookies when required.
     *
     * @param int $result The result code to return with, any result constant of {@see AuthResult}.
     * @param AuthState|null $authState=null Authentication state on success.
     *
     * @return AuthResult Authentication result object.
     */
    private function finalizeResult($result, AuthState $authState = null) {
        // Define the authentication result
        $authResult = new AuthResult($result, $authState);

        // Set a cookie if this is a new session
        if($authResult->isOk() && $authState != null && !Cookie::has(self::AUTH_COOKIE))
            Cookie::queue(
                self::AUTH_COOKIE,
                $authState->getSession()->token,
                self::SESSION_EXPIRE / 60
            );

        // Forget the session cookie if the session became invalid
        else if($authResult->isErr() && $authResult->getResult() != AuthResult::ERR_NO_SESSION)
            Cookie::forget(self::AUTH_COOKIE);

        return $authResult;
    }
}