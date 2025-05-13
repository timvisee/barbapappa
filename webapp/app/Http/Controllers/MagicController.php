<?php

namespace App\Http\Controllers;

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
            'version' => config('app.version'),
            'source' => config('app.source'),
            'env' => config('app.env'),
        ];
    }
}
