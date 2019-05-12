<?php

namespace App\Perms\Builder;

use App\Perms\AppRoles;
use App\Perms\BarRoles;
use App\Perms\CommunityRoles;

class Builder {

    /**
     * The middleware configuration components.
     */
    protected $components;

    /**
     * Construct a permissions builder.
     *
     * @param {array|null} [$components] Middleware configuration components.
     * @return {Builder} A permissions builder.
     */
    public function __construct($components = []) {
        $this->components = $components;
    }

    /**
     * Chainable way of constructing this builder.
     */
    public static function build() {
        return new Self();
    }

    /**
     * Select a permission with the given scope identifier and role ID.
     *
     * Warning: the parameter values are not validated.
     *
     * @param string $scope The permission scope identifier.
     * @parma int $id The role ID.
     */
    public function raw($scope, $id) {
        $this->components[] = $scope . ':' . $id;
        return new Config($this->components);
    }

    /**
     * Select a permission in the application scope.
     * @param {Scoped} Permissions builder for the application scope.
     */
    public function app() {
        return new Scoped(AppRoles::class, $this->components);
    }

    /**
     * Select a permission in the community scope.
     * @param {Scoped} Permissions builder for the community scope.
     */
    public function community() {
        return new Scoped(CommunityRoles::class, $this->components);
    }

    /**
     * Select a permission in the bar scope.
     * @param {Scoped} Permissions builder for the bar scope.
     */
    public function bar() {
        return new Scoped(BarRoles::class, $this->components);
    }
}
