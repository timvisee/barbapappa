<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Perms\AppRoles;
use App\Perms\BarRoles;
use App\Perms\CommunityRoles;
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
     * @param \Closure $next Next callback.
     * @param int $role The role ID to require.
     *
     * @return Response Response.
     */
    public function handle($request, Closure $next, $role = null) {
        // Ensure the user role is sufficient, show error page or continue
        // TODO: record permission failures on an audit trace
        if(!$this->evaluate($role, $request))
            return response(view('noPermission'));
        else
            return $next($request);
    }

    /**
     * Evaluate the given roles string, and evaulate whether the user has
     * permisison or not.
     *
     * If multiple roles are given, separated by a space, only one has to
     * evaluate to true. This logic is working in an OR configuration.
     *
     * @param string $config The roles configuration string, or a part of a
     *      role string. This defines what permission a user must have.
     * @param Request $request The users request.
     * @return boolean `True` if the user has proper permissions, `false` if not.
     */
    public function evaluate($config, $request) {
        // A role configuration must be specified
        if(empty(trim($config)))
            throw new \Exception("No role specified for PermsApp middleware");

        // Split each role entry by spaces
        $roles = explode(' ', $config);

        // Evaluate each role
        $allowed = false;
        foreach($roles as $role) {
            // Skip emtpy entries
            if(empty(trim($role)))
                continue;

            // Split by colon, extract the components
            $components = explode(':', $role, 2);
            $scope = $components[0];
            $role_id = $components[1];

            // Evaluate using the proper scope
            if($scope == 'app')
                $allowed = $this->evaluateScopeApp($role_id, $request);
            else if($scope == 'community')
                $allowed = $this->evaluateScopeCommunity($role_id, $request);
            else if($scope == 'bar')
                $allowed = $this->evaluateScopeBar($role_id, $request);
            else
                throw new \Exception(
                    'Could not evaluate permission, unknown permission scope specified'
                );

            // If allowed, return true
            if($allowed)
                return true;
        }

        // Return whether the user is allowed (this should always be true)
        return $allowed;
    }

    /**
     * Evaluate the given role number for the current user, in the application
     * scope.
     *
     * Note: make sure the ID of an application permission role is given, and
     * not the ID of a different permission scope.
     *
     * @param int $role The ID of the role to evaluate.
     * @param Request $request The user request.
     * @return boolean `True` if the user has permission, `false` if not.
     */
    private function evaluateScopeApp($role, $request) {
        // A role must be specified
        if(empty($role))
            throw new \Exception("Unable to evaluate role, none specified");
        if(!is_numeric($role))
            throw new \Exception("Invalid required role specified, not an integer");
        $role = (int) $role;

        // Get the current user, and it's role
        $user_role = AppRoles::NOBODY;
        if(barauth()->isAuth() && barauth()->getSessionUser()->role !== null)
            $user_role = barauth()->getSessionUser()->role;

        // Evaluate, return the result
        return $user_role >= $role;
    }

    /**
     * Evaluate the given role number for the current user, in the community
     * scope.
     *
     * Note: make sure the ID of an community permission role is given, and
     * not the ID of a different permission scope.
     *
     * @param int $role The ID of the role to evaluate.
     * @param Request $request The user request.
     * @return boolean `True` if the user has permission, `false` if not.
     */
    private function evaluateScopeCommunity($role, $request) {
        // A role must be specified
        if(empty($role))
            throw new \Exception("Unable to evaluate role, none specified");
        if(!is_numeric($role))
            throw new \Exception("Invalid required role specified, not an integer");
        $role = (int) $role;

        // Get the session user and specify the default role
        $user_role = CommunityRoles::NOBODY;
        $user = barauth()->getSessionUser();

        // Get the current community and set a default role
        if(!empty($user)) {
            $community = $request->get('community');
            if(!empty($community)) {
                // Get the user connection to this community
                $member = $community->users()
                    ->where('user_id', $user->id)
                    ->first();
                if(!empty($member))
                    $user_role = $member->role;
            }
        }

        // Evaluate, return the result
        return $user_role >= $role;
    }

    /**
     * Evaluate the given role number for the current user, in the bar
     * scope.
     *
     * Note: make sure the ID of an bar permission role is given, and
     * not the ID of a different permission scope.
     *
     * @param int $role The ID of the role to evaluate.
     * @param Request $request The user request.
     * @return boolean `True` if the user has permission, `false` if not.
     */
    private function evaluateScopeBar($role, $request) {
        // A role must be specified
        if(empty($role))
            throw new \Exception("Unable to evaluate role, none specified");
        if(!is_numeric($role))
            throw new \Exception("Invalid required role specified, not an integer");
        $role = (int) $role;

        // Get the session user and specify the default role
        $user_role = BarRoles::NOBODY;
        $user = barauth()->getSessionUser();

        // Get the current bar and set a default role
        if(!empty($user)) {
            $bar = $request->get('bar');
            if(!empty($bar)) {
                // Get the user connection to this bar
                $member = $bar->users()
                    ->where('user_id', $user->id)
                    ->first();
                if(!empty($member))
                    $user_role = $member->role;
            }
        }

        // Evaluate, return the result
        return $user_role >= $role;
    }
}
