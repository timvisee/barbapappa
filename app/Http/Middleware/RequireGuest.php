<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class RequireAuth.
 *
 * Middleware that requires the user NOT to be authenticated.
 *
 * @package App\Http\Middleware
 */
class RequireGuest {

    /**
     * Handle an incoming request.
     *
     * @param Request $request Request.
     * @param \Closure $next Next callback.
     *
     * @return Response Response.
     */
    public function handle($request, Closure $next) {
        // Redirect to the dashboard if the user is authenticated
        if(barauth()->isAuth())
            return redirect()->route('dashboard');

        return $next($request);
    }
}
