<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class HistoryTracker.
 *
 * Track the page history for the current session to allow proper back
 * navigation.
 *
 * @package App\Http\Middleware
 */
class HistoryTracker {

    /**
     * Handle the incomming request, obtain the route name for history tracking.
     *
     * @param Request $request Request.
     * @param \Closure $next Next callback.
     *
     * @return Response Response.
     */
    public function handle($request, Closure $next) {
        // TODO: get history manager service

        // TODO: remove the handle hook?
        // history()->push($request);

        return $next($request);
    }

    /**
     * Handle the incomming request, obtain the route name for history tracking.
     *
     * @param Request $request Request.
     * @param \Closure $next Next callback.
     *
     * @return Response Response.
     */
    public function terminate($request, $response) {
        // TODO: get history manager service

        if($response->isOk()) {
            history()->push($request);

            // TODO: move this into history manager
            session()->save();
        }
    }
}
