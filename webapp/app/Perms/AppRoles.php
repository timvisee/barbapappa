<?php

namespace App\Perms;

use \App\Perms\Builder\Builder;
use \App\Perms\Builder\Config;

class AppRoles {
    use Roles;

    /**
     * The scope name.
     */
    const SCOPE = 'app';

    /**
    * A nobody role.
    * Users that aren't signed in get this role.
    */
    const NOBODY = -1;

    /**
    * A normal user role.
    * The default role for signed in users.
    */
    const USER = 0;

    /**
    * The administrator role.
    * Includes permissions from `USER`.
    */
    const ADMIN = 20;

    /**
     * Role configuration preset cache.
     */
    private static $presetCache = [];

    /**
     * The roles map.
     */
    public static function roles() {
        return [
            Self::NOBODY => 'Nobody',
            Self::USER => 'User',
            Self::ADMIN => 'Admin',
        ];
    }

    /**
     * The scope name.
     */
    public static function scope() {
        return Self::SCOPE;
    }

    /**
     * A preset for application administrators.
     *
     * @return Config The permission configuration.
     */
    public static function presetAdmin() {
        return isset(Self::$presetCache[Self::ADMIN]) ?
            Self::$presetCache[Self::ADMIN] :
            Self::$presetCache[Self::ADMIN] = Builder::build()->raw(Self::SCOPE, Self::ADMIN);
    }
}
