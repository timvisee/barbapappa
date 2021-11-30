<?php

namespace App\Services\Auth;

/**
 * Class AuthResult.
 *
 * This class defines a result state object for authentications.
 *
 * @package App\Services\Auth
 */
class AuthResult {

    /**
     * Success state.
     */
    const OK = 1;

    /**
     * Error state, when no session was given.
     * This state may be returned if the current request is authenticated, and no session token was found.
     */
    const ERR_NO_SESSION = 2;

    /**
     * Error state, when an invalid token was given.
     * This state is also triggered if the token was valid, but unknown.
     */
    const ERR_INVALID_TOKEN = 3;

    /**
     * Error state, when the current session has expired.
     */
    const ERR_EXPIRED = 4;

    /**
     * Error state, when invalid credentials are given.
     * The given email address and password don't match.
     */
    const ERR_INVALID_CREDENTIALS = 5;

    /**
     * The result code.
     * @var
     */
    private $result = null;

    /**
     * The new authentication state that corresponds to this result.
     * @var AuthState
     */
    private $authState = null;

    /**
     * AuthResult constructor.
     *
     * @param int $result State value.
     * @param AuthState $authState Authentication state
     */
    public function __construct($result, AuthState $authState = null) {
        $this->result = $result;

        // Set the authentication state
        $this->authState = $authState ?? new AuthState();
    }

    /**
     * Get the state value.
     *
     * @return int State value.
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * Get the authentication state.
     *
     * @return AuthState Authentication state.
     */
    public function getAuthState() {
        return $this->authState;
    }

    /**
     * Check whether this state is ok.
     *
     * @return bool True if ok, false if error.
     */
    public function isOk() {
        return $this->result == self::OK;
    }

    /**
     * Check whether this state is an error.
     * This state might be triggered if the session has expired.
     *
     * @return bool True if error, false if ok.
     */
    public function isErr() {
        return !$this->isOk();
    }
}
