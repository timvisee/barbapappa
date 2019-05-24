<?php

namespace App\Perms\Builder;

use App\Perms\AppRoles;
use App\Perms\BarRoles;
use App\Perms\CommunityRoles;

class Config {

    /**
     * The middleware identifier for the permissions middleware.
     */
    const MIDDLEWARE_IDENTIFIER = 'perms';

    /**
     * The order of scopes, from largest to smallest.
     * This is used for inheritance checks.
     */
    const SCOPE_ORDER = [
        AppRoles::SCOPE,
        CommunityRoles::SCOPE,
        BarRoles::SCOPE,
    ];

    /**
     * The middleware configuration components.
     */
    protected $components;

    /**
     * Constructor.
     *
     * @param {Roles} $scope The scope permissions class.
     */
    public function __construct(Array $components) {
        $this->components = $components;
    }

    /**
     * Chain an OR operation:
     * This OR the following configuration.
     *
     * @return {Builder} The permissions builder.
     */
    public function or() {
        // TODO: add OR operation to components, currently not needed
        // $this->components[] = '||';
        return new Builder($this->components);
    }

    /**
     * Inherit all the same roles from larger scopes in this configuration.
     * This option should be used with care.
     *
     * If your current configuration requires role `10` (manager) in a community,
     * this method automatically inherits role `10` in the whole application.
     *
     * The role `bar:20` would inherit `community:20` and `app:20`.
     *
     * @return Config A configuration also inheriting from larger scopes.
     */
    public function inherit() {
        // Inherit from all lower scopes for each component, then optimize
        return $this->optimize(
            collect($this->components)
                ->flatMap(function($component) {
                    $parts = explode(':', trim($component), 2);

                    return collect(Self::SCOPE_ORDER)
                        ->take(array_search($parts[0], Self::SCOPE_ORDER))
                        ->map(function($scope) use($parts) {
                            return $scope . ':' . $parts[1];
                        })
                        ->push($component);
                })
            );
    }

    /**
     * Optimize the role configuration.
     * If it could not be optimized, the same configuration is returned.
     *
     * This removes obsolete components. The following components are considered
     * obsolete:
     * - duplicate entries
     * - entries covered by other components
     *   (`bar:20` would be obsolete if `bar:10` was set as well)
     *
     * @param array [$components=null] Optionally pass the list of components to
     *      optimize, instead of optimizing in the current configuration.
     *
     * @return Config An optimized configuration.
     */
    public function optimize($components = null) {
        return new Config(
            collect($components ?? $this->components)

                // Collect role IDs from components per scope
                ->mapToGroups(function($component) {
                    $parts = explode(':', trim($component), 2);
                    return [$parts[0] => $parts[1]];
                })

                // Keep only the smallest role per scope
                ->map(function($ids, $scope) {
                    return $scope . ':' . $ids->min();
                })
                ->toArray()
            );
    }

    /**
     * Build the configuration string to use for this configuration.
     * This cannot be used for middleware, as it's missing the middleware
     * identifier. Use `middleware()` instead.
     */
    public function build() {
        return implode(' ', $this->components);
    }

    /**
     * Build the middleware string to use for this configuration.
     */
    public function middleware() {
        return Self::MIDDLEWARE_IDENTIFIER . ':' . $this->build();
    }
}
