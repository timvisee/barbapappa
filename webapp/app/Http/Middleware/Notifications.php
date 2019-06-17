<?php

namespace App\Http\Middleware;

use App\Models\Notifications\Notification;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class Notifications.
 *
 * Middleware that loads notification details for the current user, which are
 * passed to the notification view.
 *
 * @package App\Http\Middleware
 */
class Notifications {

    /**
     * Handle an incoming request.
     *
     * @param Request $request Request.
     * @param \Closure $next Next callback.
     *
     * @return Response Response.
     */
    public function handle($request, Closure $next) {
        // Get the user, require to be logged in
        $user = barauth()->getUser();
        if(is_null($user))
            return $next($request);

        // Build a list of notification counts
        $notifications = Notification::select('persistent', 'read_at')->get();
        $counts = [
            'unread' => 0,
            'persistent' => 0,
            'all' => 0,
        ];
        foreach($notifications as $notification) {
            if($notification->read_at == null)
                $counts['unread']++;
            if($notification->persistent)
                $counts['persistent']++;
            $counts['all']++;
        }

        // Make the notification counts available in the request and views
        $request->attributes->add(['notificationCounts' => $counts]);
        view()->share('notificationCounts', $counts);

        return $next($request);
    }
}
