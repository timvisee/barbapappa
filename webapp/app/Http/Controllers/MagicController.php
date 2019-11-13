<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use Illuminate\Http\Request;

class MagicController extends Controller {

    /**
     * Heartbeat endpoint.
     */
    public function heartbeat() {
        return 'OK';
    }

    /**
     * Version endpoint.
     */
    public function version() {
        return [
            'version' => config('app.version_name'),
            'version_code' => config('app.version_code'),
            'source' => config('app.source'),
            'env' => config('app.env'),
        ];
    }
}
