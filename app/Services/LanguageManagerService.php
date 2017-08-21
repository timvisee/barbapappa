<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

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

        // TODO: Figure out the current language of the user
    }

    /**
     * Use the locale selected by the user in the current session.
     *
     * If no locale was selected, nothing will happen.
     */
    public function useSessionLocale() {
        // Check whether a locale cookie exists
        if(!Cookie::has(self::LOCALE_COOKIE))
            return;

        // Get the selected locale
        $locale = Cookie::get(self::LOCALE_COOKIE);

        // Set the locale, safely
        try {
            $this->setLocale($locale, false, false);
        } catch (\Exception $e) {}
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
     *
     * @param User $user User to get the locale for.
     * @param string|null $default Default locale value, returned when the user doesn't have a locale selected.
     *
     * @return string|null The locale.
     */
    public function getUserLocale(User $user, $default = null) {
        return $user != null ? ($user->locale ?: $default) : null;
    }

    /**
     * Get the locale selected by the given user.
     * If the user doesn't have a selected locale, the default application locale is returned.
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
     *
     * @param string|null $default=null The default locale to return if none is selected.
     *
     * @return string|null The locale.
     */
    public function getLocale($default = null) {
        return $this->locale ?: $default;
    }

    /**
     * Get the currently selected locale.
     *
     * The default application locale is returned if no locale was selected.
     *
     * @return string The locale.
     */
    public function getLocaleSafe() {
        return $this->getLocale(
            $this->getDefaultLocale()
        );
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
        return in_array($locale, $this->getAvailableLocales());
    }

    /**
     * Get an array of available locales.
     *
     * @return array Available locale strings.
     */
    public function getAvailableLocales() {
        return config('app.locales');
    }
}