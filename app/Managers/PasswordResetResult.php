<?php

namespace App\Managers;

/**
 * Class PasswordResetResult.
 *
 * This class defines a result state for password resets.
 *
 * @package App\Services\Auth
 */
class PasswordResetResult {

    /**
     * Success state.
     */
    const OK = 1;

    /**
     * Error state, when no token was given.
     * This state is returned when no reset token is given.
     */
    const ERR_NO_TOKEN = 2;

    /**
     * Error state, when an invalid token is given.
     * This state is returned when an invalid token is given.
     * The token might be unknown because it has expired.
     */
    const ERR_INVALID_TOKEN = 3;

    /**
     * Error state, when the given token has already been used.
     * This state is returned when the given token has already been used.
     */
    const ERR_USED_TOKEN = 4;

    /**
     * The result code.
     * @var
     */
    private $result = null;

    /**
     * EmailVerifyResult constructor.
     *
     * @param int $result State value.
     */
    public function __construct($result) {
        $this->result = $result;
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