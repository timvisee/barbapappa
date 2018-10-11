<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Perms\AppRoles;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class PermsApp.
 *
 * Middleware for permission checking, to require users to have a certain
 * application permission.
 *
 * @package App\Http\Middleware
 */
class PermsApp {

    /**
     * Handle an incoming request.
     *
     * @param Request $request Request.
     * @param \Closure $next Next callback.
     * @param int $role The role ID to require.
     *
     * @return Response Response.
     */
    public function handle($request, Closure $next, $role = null) {
        // A role must be specified
        if($role === null)
            throw new \Exception("No role specified for PermsApp middleware");
        if(!is_numeric($role))
            throw new \Exception("Invalid specified for PermsApp middleware, not an integer");
        $role = (int) $role;

        // Get the current user, and it's role
        $user_role = AppRoles::NOBODY;
        if(barauth()->isAuth() && barauth()->getSessionUser()->role !== null)
            $user_role = barauth()->getSessionUser()->role;

        // Ensure the user role is sufficient
        if($user_role < $role)
            // TODO: record permission failures on an audit trace
            return response(view('noPermission'));

        // Continue
        return $next($request);
    }
}
