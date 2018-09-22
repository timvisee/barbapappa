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
    const EMAIL_RESET_TOKEN = 'string|size:32';

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
     * Permission group name validation configuration.
     */
    const PERMISSION_GROUP_NAME = 'string|min:2|max:255';
}
