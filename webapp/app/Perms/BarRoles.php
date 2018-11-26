<?php

namespace App\Perms;

use \App\Perms\Builder\Builder;
use \App\Perms\Builder\Config;

class BarRoles {
    use Roles;

    /**
     * The scope name.
     */
    const SCOPE = 'bar';

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
    * The manager role.
    * Includes permissions from `NORMAL`.
    */
    const MANAGER = 10;

    /**
    * The administrator role.
    * Includes permissions from `MANAGER`.
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
            Self::MANAGER => 'Manager',
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
     * A preset for bar managers.
     * This includes application and communtiy administrators.
     *
     * @return Config The permission configuration.
     */
    public static function presetManager() {
        return isset(Self::$presetCache[Self::MANAGER]) ?
            Self::$presetCache[Self::MANAGER] :
            Self::$presetCache[Self::MANAGER] = CommunityRoles::presetAdmin()
                ->or()
                ->raw(Self::SCOPE, Self::MANAGER);
    }

    /**
     * A preset for bar administrators.
     * This includes application and communtiy administrators.
     *
     * @return Config The permission configuration.
     */
    public static function presetAdmin() {
        return isset(Self::$presetCache[Self::ADMIN]) ?
            Self::$presetCache[Self::ADMIN] :
            Self::$presetCache[Self::ADMIN] = CommunityRoles::presetAdmin()
            ->or()
            ->raw(Self::SCOPE, Self::ADMIN);
    }
}
