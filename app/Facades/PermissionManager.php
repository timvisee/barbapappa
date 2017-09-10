<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PermissionManager extends Facade {

    protected static function getFacadeAccessor() {
        return 'perms';
    }
}