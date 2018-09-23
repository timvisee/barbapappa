<?php

namespace App\Http\Middleware;

use App\Models\Bar;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class SelectBar.
 *
 * Middleware that allows selecting a bar through an URL parameter.
 *
 * @package App\Http\Middleware
 */
class SelectBar {

    /**
     * Handle an incoming request.
     *
     * @param Request $request Request.
     * @param \Closure $next Next callback.
     *
     * @return Response Response.
     */
    public function handle($request, Closure $next) {
        // TODO: require sufficient permissions to view

        // Get the bar ID, find it
        $barId = $request->route('barId');
        $bar = Bar::smartFindOrFail($barId);

        // Make selected bar available in the request and views
        $request->attributes->add(['bar' => $bar]);
        view()->share('bar', $bar);

        // Continue
        return $next($request);
    }
}
