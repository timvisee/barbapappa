<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Class RequireAuth.
 *
 * Middleware that requires the user to be authenticated.
 *
 * @package App\Http\Middleware
 */
class RequireAuth {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null) {
        // Redirect to the login page if not authenticated
        // TODO: Add the current URL as redirect URL
        if(!barauth()->isAuth())
            return redirect()->route('login');

        return $next($request);
    }
}
