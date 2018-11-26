<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class BarAuth extends Facade {

    protected static function getFacadeAccessor() {
        return 'barauth';
    }
}