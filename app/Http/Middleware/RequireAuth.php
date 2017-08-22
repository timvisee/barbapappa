<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * @param Request $request Request.
     * @param \Closure $next Next callback.
     *
     * @return Response Response.
     */
    public function handle($request, Closure $next) {
        // Redirect to the login page if not authenticated
        // TODO: Add the current URL as redirect URL
        if(!barauth()->isAuth())
            return redirect()
                ->route('login')
                ->with('error', __('auth.authRequired'));

        return $next($request);
    }
}
