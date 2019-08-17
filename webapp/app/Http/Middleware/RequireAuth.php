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
        // Determine login route
        $route = config('app.auth_session_link') ? 'index' : 'login';

        // Redirect to the login page if not authenticated
        if(!barauth()->isAuth())
            return redirect()
                ->guest(route($route))
                ->with('error', __('auth.authRequired'));

        return $next($request);
    }
}
