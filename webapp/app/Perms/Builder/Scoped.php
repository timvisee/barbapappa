<?php

namespace App\Perms\Builder;

class Scoped {

    /**
     * The scope permissions class.
     */
    protected $scope;

    /**
     * The middleware configuration components.
     */
    protected $components;

    /**
     * Constructor.
     *
     * @param {Roles} $scope The scope permissions class.
     */
    public function __construct($scope, $components) {
        $this->scope = $scope;
        $this->components = $components;
    }

    /**
     * Dynamic function, to catch role names.
     */
    public function __call($method, $params) {
        // Attempt to find the role ID, add the component
        $scope = $this->scope;
        $id = $scope::fromName($method);
        $this->components[] = $scope::scope() . ':' . $id;

        // Return a perms config
        return new Config($this->components);
    }
}
