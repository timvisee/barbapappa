<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class LangManager extends Facade {

    protected static function getFacadeAccessor() {
        return 'langManager';
    }
}