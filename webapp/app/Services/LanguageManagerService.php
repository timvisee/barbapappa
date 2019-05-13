<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\HtmlString;

class LanguageManagerService {

    /**
     * Cookie used to remember the user's locale.
     */
    const LOCALE_COOKIE = 'locale';

    /**
     * The time it takes for the locale cookie to expire.
     */
    const LOCALE_COOKIE_EXPIRE = 6 * 30 * 24 * 60 * 60; // ~ 6 months

    /**
     * Application instance.
     * @var Application
     */
    private $app;

    /**
     * The locale to use for the current session.
     * @var
     */
    private $locale = null;

    /**
     * Language service constructor.
     *
     * @param Application $app Application instance.
     */
    public function __construct(Application $app) {
        $this->app = $app;

        // Use the locale from the session
        $this->useSessionLocale();
    }

    /**
     * Use the locale selected by the user in the current session.
     *
     * If no locale was selected, nothing will happen.
     */
    public function useSessionLocale() {
        // Get the session locale, and set it
        $locale = $this->getSessionLocale(null);
        if($locale != null)
            $this->setLocale($locale, false, false);
    }

    /**
     * Get the locale selected in this session.
     * If no locale was selected, the given default is returned.
     *
     * @param string|null $default=null The default to return.
     * @return string|null The locale or null if not selected.
     */
    public function getSessionLocale($default = null) {
        // Return the default if no cookie is set
        if(!Cookie::has(self::LOCALE_COOKIE))
            return $default;

        // Get the locale
        $locale = Cookie::get(self::LOCALE_COOKIE);

        // Return it if valid
        return $this->isValidLocale($locale) ? $locale : (
            $this->isValidLocale($default) ? $default : null
        );
    }

    /**
     * Get the locale selected in this session.
     * If no locale was selected, the application default locale is returned.
     *
     * This method is safe because it always returns any locale.
     *
     * @return string The locale.
     */
    public function getSessionLocaleSafe() {
        return $this->getSessionLocale($this->getDefaultLocale());
    }

    /**
     * Use the locale selected by the user on it's account.
     *
     * If no locale was configured by the user, nothing will happen.
     *
     * @param User $user The user to use the locale from.
     */
    public function useUserLocale(User $user) {
        // Check whether the user has a selected locale
        if(empty($this->getUserLocale($user)))
            return;

        // Set the locale, safely
        try {
            $this->setLocale(
                $this->getUserLocaleSafe($user),
                true,
                false
            );
        } catch (\Exception $e) {}
    }

    /**
     * Get the locale selected by the given user.
     * If the user doesn't have a locale selected, the given default is returned.
     *
     * @param User $user User to get the locale for.
     * @param string|null $default=null The default locale returned if no locale was selected.
     *
     * @return string|null The locale or null.
     */
    public function getUserLocale(User $user, $default = null) {
        // Validate the default value
        if(!$this->isValidLocale($default))
            $default = null;

        // The user must be valid
        if($user == null)
            return $default;

        // Get the locale, return it if valid
        $locale = $user->locale;
        return $this->isValidLocale($locale) ? $locale : $default;
    }

    /**
     * Get the locale selected by the given user.
     * If the user doesn't have a locale selected the application default is returned.
     *
     * This method is safe because a locale is always returned.
     *
     * @param User $user User to get the locale for.
     *
     * @return string The locale.
     */
    public function getUserLocaleSafe(User $user) {
        return $this->getUserLocale(
            $user,
            $this->getDefaultLocale()
        );
    }

    /**
     * Get the currently selected locale.
     * If no locale is currently selected, the given default is returned.
     *
     * @param string|null $default=null The default locale to return if nothing is selected.
     *
     * @return string|null The locale or null.
     */
    public function getLocale($default = null) {
        // Return the valid locale
        return $this->isValidLocale($this->locale) ? $this->locale : (
            $this->isValidLocale($default) ? $default : null
        );
    }

    /**
     * Get the currently selected locale.
     * If no locale is currently selected, the application default is returned.
     *
     * This method is safe because a locale is always returned.
     *
     * @return string The locale.
     */
    public function getLocaleSafe() {
        return $this->getLocale($this->getDefaultLocale());
    }

    /**
     * Check whether the current user has selected it's preferred locale.
     *
     * @return bool True if the user has a locale selected, false if not.
     */
    public function hasSelectedLocale() {
        return !empty($this->locale);
    }

    /**
     * Set the active locale.
     *
     * @param string $locale Locale to use.
     * @param boolean $rememberCookie True to remember this locale choice in a cookie, false to not remember.
     * @param boolean $rememberUser True to remember this locale choice for the user, false to not remember.
     *
     * @throws \Exception Throws if an invalid locale is given.
     */
    public function setLocale($locale, $rememberCookie, $rememberUser) {
        // Trim the locale
        $locale = trim($locale);

        // The locale must be valid
        if(!$this->isValidLocale($locale))
            throw new \Exception('Invalid locale given, locale is not known or supported');

        // Set the locale
        $this->locale = $locale;

        // Set the locale in laravel
        App::setLocale($locale);

        // Queue a cookie to be set to remember the selected locale
        if($rememberCookie)
            Cookie::queue(
                Cookie::make(
                    self::LOCALE_COOKIE,
                    $locale,
                    self::LOCALE_COOKIE_EXPIRE / 60
                )
            );

        // Remember the locale for a signed in user
        if($rememberUser && barauth()->isAuth()) {
            $user = barauth()->getSessionUser();
            $user->locale = $locale;
            $user->save();
        }
    }

    /**
     * Get the application default locale.
     *
     * @return string Default locale.
     */
    public function getDefaultLocale() {
        return config('app.locale');
    }

    /**
     * Check whether the given locale is valid and supported.
     *
     * @param string $locale Locale to check for.
     *
     * @return boolean True if the locale is valid and supported, false if not.
     */
    public function isValidLocale($locale) {
        return $locale != null && in_array($locale, $this->getLocales(true, true));
    }

    /**
     * Check whether the given locale is hidden by default.
     *
     * @param string $locale Locale to check for.
     * @param boolean [$hideCurrent=false] False to never hide it if it's the
     *      current locale.
     *
     * @return boolean True if the given locale is hidden, false if not.
     */
    public function isHiddenLocale($locale, $hideCurrent = false) {
        return $locale == null || (in_array($locale, config('app.hidden_locales')) && !$hideCurrent && $this->locale != $locale);
    }

    /**
     * Get an array of available locales.
     * This lists only the visible locales by default.
     *
     * If $user is set to true, and the user currently has a hidden locale selected.
     * That locale is included in the list too.
     *
     * If $hidden is set to true, all locales are returned.
     *
     * @param bool [$user=true] True to get all locales visible to the user.
     * @param bool [$hidden=false] True to get all locales, including all hidden ones.
     *
     * @return array Available locale strings.
     */
    public function getLocales($user = true, $hidden = false) {
        return array_filter(config('app.locales'), function($locale) use($user, $hidden) {
            return $hidden || !$this->isHiddenLocale($locale);
        });
    }

    /**
     * Get the flag identifier for the given locale.
     *
     * Examples:
     * - For a Dutch flag, 'nl' would be returned.
     * - For an English flag, 'gb' would probably be returned.
     *
     * If an invalid locale is given, the users locale or the application default locale is used.
     *
     * @param string|null $locale=null Locale to get the flag identifier for. Null to use the user or application default locale.
     *
     * @return string Flag identifier.
     */
    public function getLocaleFlagIdentifier($locale = null) {
        // The locale must be valid
        if($locale == null || !$this->isValidLocale($locale))
            $locale = $this->getLocaleSafe();

        // Get the identifier
        return trans('lang.iconName', [], $locale);
    }

    /**
     * Get the class of the flag icon for the given locale.
     *
     * If an invalid locale is given, the users locale or the application default locale is used.
     *
     * @param string|null $locale=null Locale to get the flag class for. Null to use the user or application default locale.
     * @param boolean $square=false True to render a square flag, otherwise a 4:3 flag is rendered.
     * @param boolean $rounded=true True to render the flag with rounded corners, false to use sharp corners.
     *
     * @return string Flag class.
     */
    public function getLocaleFlagClass($locale = null, $square = false, $rounded = true) {
        return 'flag-icon flag-icon-' . $this->getLocaleFlagIdentifier($locale)
            . ($square ? ' flag-icon-squared' : '')
            . ($rounded ? ' flag-icon-rounded' : '');
    }

    /**
     * Render a flag as HTML element.
     *
     * If an invalid locale is given, the users locale or the application default locale is used.
     *
     * @param string|null $locale=null Locale to get the flag class for. Null to use the user or application default locale.
     * @param boolean $square=false True to render a square flag, otherwise a 4:3 flag is rendered.
     * @param boolean $rounded=true True to render the flag with rounded corners, false to use sharp corners.
     *
     * @return HtmlString Element which renders the flag.
     */
    public function renderFlag($locale = null, $square = false, $rounded = true) {
        return new HtmlString('<span class="' . $this->getLocaleFlagClass($locale, $square, $rounded) . '"></span>');
    }
}
