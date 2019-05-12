<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class Perms.
 *
 * Middleware for permission checking, to require users to have certain
 * configured permissions in the `app`, `community` or `bar` scopes.
 *
 * @package App\Http\Middleware
 */
class Perms {

    /**
     * Handle an incoming request.
     *
     * @param Request $request Request.
     * @param Closure $next Next callback.
     * @param int $config The permission role configuration to require.
     *
     * @return Response Response.
     */
    public function handle(Request $request, Closure $next, $config = null) {
        // Ensure the user role is sufficient, show error page or continue
        // TODO: return 403 forbidden
        if(!perms($config, $request))
            return response(view('noPermission'));
        else
            return $next($request);
    }
}
