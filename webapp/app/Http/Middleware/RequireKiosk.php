<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class RequireKiosk.
 *
 * Middleware that requires the session to the authenticated as kiosk.
 *
 * @package App\Http\Middleware
 */
class RequireKiosk {

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
        if(!kioskauth()->isAuth())
            return redirect()
                ->guest(route($route))
                ->with('error', __('auth.authRequired'));

        return $next($request);
    }
}
