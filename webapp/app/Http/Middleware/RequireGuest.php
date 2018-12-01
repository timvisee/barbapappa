<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class RequireGuest.
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
        // Redirect to the previous page if not logged in, fallback to the dashboard
        if(barauth()->isAuth()) {
            if(url()->previous() != url()->current())
                $url = url()->previous();
            else
                $url = route('dashboard');
            return redirect($url)->with('error', __('auth.guestRequired'));
        }

        return $next($request);
    }
}
