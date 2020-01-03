<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class SelectLocale.
 *
 * Middleware that redirect the user to language selection if not selected yet.
 *
 * @package App\Http\Middleware
 */
class SelectLocale {

    /**
     * Handle an incoming request.
     *
     * @param Request $request Request.
     * @param \Closure $next Next callback.
     *
     * @return Response Response.
     */
    public function handle(Request $request, Closure $next) {
        // User must have locale selected
        if(!langManager()->hasSelectedLocale() && !$request->routeIs('language')) {
            // Guess preferred user locale based on browser headers
            $locales = langManager()->getLocales();
            $locale = $request->getPreferredLanguage($locales);

            // Show language select page if no locale could be determined
            if(empty($locale))
                return redirect()->guest(route('language'));

            // Set automatically selected locale
            langManager()->setLocale($locale, true, false);
        }

        return $next($request);
    }
}
