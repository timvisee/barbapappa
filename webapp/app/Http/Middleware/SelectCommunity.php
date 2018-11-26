<?php

namespace App\Http\Middleware;

use App\Models\Community;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class SelectCommunity.
 *
 * Middleware that allows selecting a community through an URL parameter.
 *
 * @package App\Http\Middleware
 */
class SelectCommunity {

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

        // Get the community ID, find it
        $communityId = $request->route('communityId');
        $community = Community::smartFindOrFail($communityId);

        // Make selected community available in the request and views
        $request->attributes->add(['community' => $community]);
        view()->share('community', $community);

        // Continue
        return $next($request);
    }
}
