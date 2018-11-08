<?php

namespace App\Helpers;

use Illuminate\Validation\Rule;

use \App\Models\Community;

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
     * User password validation configuration.
     */
    const USER_PASSWORD = 'string|min:6|max:4096';

    /**
     * Simple password validation configuration.
     * This is used for community and bar passwords, and are less constrained.
     */
    const SIMPLE_PASSWORD = 'string|min:4|max:4096';

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
     * A regular name.
     * For example, a community or bar name.
     */
    const NAME = 'string|min:2|max:255';

    /**
     * First name validation configuration.
     */
    const FIRST_NAME = self::NAME;

    /**
     * Last name validation configuration.
     */
    const LAST_NAME = self::NAME;

    /**
     * Base slug validation configuration.
     */
    const SLUG = 'string|alpha_dash|min:2|max:64|regex:' . self::SLUG_REGEX;

    /**
     * A regex for slug validation.
     */
    const SLUG_REGEX = '/^[a-zA-Z_][a-zA-Z0-9_-]{1,64}$/';

    /**
     * A protection code for a community or bar.
     */
    const CODE = 'string|min:2|max:4096';

    /**
     * Build the community slug validation configuration.
     *
     * @param int|null $community The community this configuration is built for.
     * @return string The validation configuration.
     */
    public static function communitySlug($community = null) {
        // Buid the uniqueness rule, ignore the current if given
        $unique = Rule::unique('communities', 'slug');
        if(!empty($community))
            $unique = $unique->ignore($community->id);

        return self::SLUG . '|' . $unique;
    }

    /**
     * Build the bar slug validation configuration.
     *
     * @param int|null $bar The bar this configuration is built for.
     * @return string The validation configuration.
     */
    public static function barSlug($bar = null) {
        // Buid the uniqueness rule, ignore the current if given
        $unique = Rule::unique('bars', 'slug');
        if(!empty($bar))
            $unique = $unique->ignore($bar->id);

        return self::SLUG . '|' . $unique;
    }

    /**
     * Build the community economy validation configuration.
     *
     * @param int $community The community this configuration is built for.
     * @return Rule The validation rule.
     */
    public static function communityEconomy(Community $community) {
        return Rule::exists('economies', 'id')
            ->where(function($query) use($community) {
                return $query->where('community_id', $community->id);
            });
    }
}
