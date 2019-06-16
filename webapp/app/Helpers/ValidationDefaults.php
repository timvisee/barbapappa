<?php

namespace App\Helpers;

use Illuminate\Validation\Rule;

use \App\Models\Community;
use \App\Models\Economy;
use \App\Perms\AppRoles;
use \App\Perms\BarRoles;
use \App\Perms\CommunityRoles;

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
     * A description.
     */
    const DESCRIPTION = 'string|max:2048';

    /**
     * A price value, with two optional decimal digits, may be zero.
     */
    const PRICE = 'regex:/^(\d{0,8}([,.]\d{1,2})?)?$/';

    /**
     * A price value, with two optional decimal digits, may only be positive.
     */
    const PRICE_POSITIVE = 'regex:/^('
            . '[1-9][0-9]{0,7}([,.]\d{1,2})?|'
            . '\d{0,8}[,.]([0-9][1-9]|[1-9][0-9]?)'
        . ')?$/';

    /**
     * bunq API token.
     */
    const BUNQ_TOKEN = 'string|alpha_num|size:64';

    /**
     * Build the community slug validation configuration.
     *
     * @param int|null $community The community this configuration is built for.
     * @return string The validation configuration.
     */
    public static function communitySlug($community = null) {
        // Build the uniqueness rule, ignore the current if given
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
        // Build the uniqueness rule, ignore the current if given
        $unique = Rule::unique('bars', 'slug');
        if(!empty($bar))
            $unique = $unique->ignore($bar->id);

        return self::SLUG . '|' . $unique;
    }

    /**
     * Build the community economy validation configuration.
     *
     * This checks whether the submitted economy is part of the given community.
     *
     * @param int $community The community this configuration is built for.  @return Rule The validation rule.
     */
    public static function communityEconomy(Community $community) {
        return Rule::exists('economies', 'id')
            ->where(function($query) use($community) {
                // Scope to the current community
                return $query->where('community_id', $community->id);
            });
    }

    /**
     * Build the economy currency validation configuration.
     *
     * This checks whether the submitted currency within an economy is unique,
     * and not yet configured.
     *
     * Note: this function returns an array of validation rules.
     *
     * @param int $economy The economy this configuration is built for.
     * @param bool [$unique=true] True to do the unique check, false if not.
     *
     * @return Array An array of validation rules.
     */
    public static function economyCurrency(Economy $economy, $unique = true) {
        $rules = [Rule::exists('economy_currencies', 'id')];

        if($unique)
            $rules[] = Rule::unique('economy_currencies', 'currency_id')
                    ->where(function($query) use($economy) {
                        // Scope to the current economy
                        return $query->where('economy_id', $economy->id);
                    });

        return $rules;
    }

    /**
     * Build the wallet currency validation configuration.
     *
     * This checks whether the submitted currency for a wallet exists and allows
     * wallet creation.
     *
     * Note: this function returns an array of validation rules.
     *
     * @param int $economy The economy this configuration is built for.
     * @return Array An array of validation rules.
     */
    public static function walletEconomyCurrency(Economy $economy) {
        return [
            Rule::exists('economy_currencies', 'id')
                ->where(function($query) use($economy) {
                    // Scope to the current economy and to allowed wallet creation
                    return $query
                        ->where('economy_id', $economy->id)
                        ->where('allow_wallet', true);
                }),
        ];
    }

    /**
     * Build a validator configuration for application role IDs.
     *
     * @return string The validation configuration.
     */
    public static function appRoles() {
        return Rule::in(AppRoles::ids());
    }

    /**
     * Build a validator configuration for community role IDs.
     *
     * @return string The validation configuration.
     */
    public static function communityRoles() {
        return Rule::in(CommunityRoles::ids());
    }

    /**
     * Build a validator configuration for bar role IDs.
     *
     * @return string The validation configuration.
     */
    public static function barRoles() {
        return Rule::in(BarRoles::ids());
    }
}
