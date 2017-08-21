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
    public function handle($request, Closure $next) {
        // Redirect to the locale selection screen if needed, don't redirect if already on the language page
        // TODO: Add the current URL as redirect URL
        if(!langManager()->hasSelectedLocale() && !$request->routeIs('language'))
            return redirect()->route('language');

        return $next($request);
    }
}
