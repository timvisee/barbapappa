<?php

namespace App\Helpers;

/**
 * Class ValidationDefaults.
 *
 * This class defines the default validation configurations to use.
 *
 * @package App\Helpers
 */
class ValidationDefaults {

    /**
     * Email validation configuration.
     */
    const EMAIL = 'string|email|max:255';

    /**
     * Password validation configuration.
     */
    const PASSWORD = 'string|min:6|max:4096';

    /**
     * Email reset token validation configuration.
     */
    const EMAIL_RESET_TOKEN = 'string|alpha_num|size:32';

    /**
     * Email reset token validation configuration.
     */
    const EMAIL_VERIFY_TOKEN = self::EMAIL_RESET_TOKEN;

    /**
     * Password reset token validation configuration.
     */
    const PASSWORD_RESET_TOKEN = 'string|size:32';

    /**
     * First name validation configuration.
     */
    const FIRST_NAME = 'string|min:2|max:255';

    /**
     * Last name validation configuration.
     */
    const LAST_NAME = 'string|min:2|max:255';

    /**
     * Base slug validation configuration.
     */
    const SLUG = 'string|alpha_dash|min:2|max:64|regex:' . self::SLUG_REGEX;

    /**
     * A regex for slug validation.
     */
    const SLUG_REGEX = '/^[a-zA-Z_][a-zA-Z0-9_-]{1,64}$/';

    /**
     * Community slug validation configuration.
     */
    const COMMUNITY_SLUG = self::SLUG . '|unique:communities,slug';

    /**
     * Bar slug validation configuration.
     */
    const BAR_SLUG = self::SLUG . '|unique:bars,slug';

    /**
     * A protection code for a community or bar.
     */
    const CODE = 'string|min:2|max:4096';
}
