<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Logo extends Facade {

    protected static function getFacadeAccessor() {
        return 'logo';
    }
}