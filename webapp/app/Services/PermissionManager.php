<?php

namespace App\Services;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\User;
use App\Perms\AppRoles;
use App\Perms\BarRoles;
use App\Perms\CommunityRoles;
use App\Perms\Builder\Config as PermsConfig;

class PermissionManager {

    /**
     * Application instance.
     * @var Application
     */
    private $app;

    /**
     * A cache map for community user roles.
     * This map holds: `community_id:user_id -> role`
     */
    private $communityUserRoles = [];

    /**
     * A cache map for bar user roles.
     * This map holds: `bar_id -> user_id -> role`
     */
    private $barUserRoles = [];

    /**
     * BarAuthManager constructor.
     *
     * @param Application $app Application instance.
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }

    /**
     * Evaluate the given roles string, and evaulate whether the user has
     * permisison or not.
     *
     * If multiple roles are given, separated by a space, only one has to
     * evaluate to true. This logic is working in an OR configuration.
     *
     * TODO: record permission failures on an audit trace
     *
     * @param string|PermsConfig $config The roles configuration string, or a part of a
     *      role string. This defines what permission a user must have.
     * @param Request $request The users request.
     * @return boolean `True` if the user has proper permissions, `false` if not.
     */
    public function evaluate($config, Request $request) {
        // Parse the configuration, a role configuration must be specified
        if($config instanceof PermsConfig)
            $config = $config->build();
        else if(empty(trim($config)))
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
                $allowed = $this->evaluateScopeApp($role_id);
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
     * @return boolean `True` if the user has permission, `false` if not.
     */
    private function evaluateScopeApp($role) {
        // A role must be specified
        if(empty($role) && $role !== "0" && $role !== 0)
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
    private function evaluateScopeCommunity($role, Request $request) {
        // A role must be specified
        if(empty($role) && $role !== "0" && $role !== 0)
            throw new \Exception("Unable to evaluate role, none specified");
        if(!is_numeric($role))
            throw new \Exception("Invalid required role specified, not an integer");
        $role = (int) $role;

        // Get the session user and specify the default role
        $user_role = CommunityRoles::NOBODY;
        $user = barauth()->getSessionUser();

        // Get current community and users role if set, use cache or query
        if(!empty($user)) {
            $community = $request->get('community');
            if(!empty($community)) {
                if(isset($this->communityUserRoles[$user->id][$community->id]))
                    $user_role = $this->communityUserRoles[$user->id][$community->id];
                else {
                    // Query the user connection, cache and set the result
                    $member = $community->memberUsers(['role'], false)
                        ->where('user_id', $user->id)
                        ->first([]);
                    if(!empty($member))
                        $user_role = $this->communityUserRoles[$user->id][$community->id] = $member->pivot->role;
                }
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
    private function evaluateScopeBar($role, Request $request) {
        // A role must be specified
        if(empty($role) && $role !== "0" && $role !== 0)
            throw new \Exception("Unable to evaluate role, none specified");
        if(!is_numeric($role))
            throw new \Exception("Invalid required role specified, not an integer");
        $role = (int) $role;

        // Get the session user and specify the default role
        $user_role = BarRoles::NOBODY;
        $user = barauth()->getSessionUser();

        // Get current bar and users role if set, use cache or query
        if(!empty($user)) {
            $bar = $request->get('bar');
            if(!empty($bar)) {
                if(isset($this->barUserRoles[$user->id][$bar->id]))
                    $user_role = $this->barUserRoles[$user->id][$bar->id];
                else {
                    // Query the user connection, cache and set the result
                    $member = $bar->memberUsers(['role'], false)
                        ->where('user_id', $user->id)
                        ->first([]);
                    if(!empty($member))
                        $user_role = $this->barUserRoles[$user->id][$bar->id] = $member->pivot->role;
                }
            }
        }

        // Evaluate, return the result
        return $user_role >= $role;
    }

    /**
     * Flush permission caches.
     */
    public function flush() {
        $this->communityUserRoles = [];
        $this->barUserRoles = [];
    }
}
