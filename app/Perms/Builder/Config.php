<?php

namespace App\Perms\Builder;

class Config {

    /**
     * The middleware identifier for the permissions middleware.
     */
    const MIDDLEWARE_IDENTIFIER = 'perms';

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

    public function __call($method, $params) {
        if($method == 'or')
            return $this->_or();
    }

    /**
     * Chain an OR operation:
     * This OR the following configuration.
     *
     * @return {Builder} The permissions builder.
     */
    public function _or() {
        // TODO: add OR operation to components, currently not needed
        // $this->components[] = '||';
        return new Builder($this->components);
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
