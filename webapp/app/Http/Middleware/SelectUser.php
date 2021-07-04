<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class SelectUser.
 *
 * Middleware that allows selecting a user through an URL parameter, to use on the accounts page for
 * example.
 *
 * @package App\Http\Middleware
 */
class SelectUser {

    /**
     * Handle an incoming request.
     *
     * @param Request $request Request.
     * @param \Closure $next Next callback.
     *
     * @return Response Response.
     */
    public function handle($request, Closure $next) {
        // TODO: require sufficient permissions if user is not self

        // Get the selected user, use authenticated user as default if not specified
        $user = $request->route('userId');
        if($user == null || $user == '-')
            $user = barauth()->getSessionUser();
        else
            $user = User::findOrFail($user);

        // Determine whehter we're provindg a user other than the authenticated user
        $isOtherUser = barauth()->getUser()->id != $user->id;

        // Make selected user available in the request and views
        $request->attributes->add(['user' => $user, 'isOtherUser' => $isOtherUser]);
        view()->share('user', $user);
        view()->share('isOtherUser', $isOtherUser);

        // Continue
        return $next($request);
    }
}
