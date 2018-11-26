<?php

use Illuminate\Support\Facades\App;

// Set the locale to use
if(isset($locale))
    App::setLocale($locale);
