<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Notifications\Notification;
use App\Perms\Builder\Config as PermsConfig;
use App\Perms\CommunityRoles;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Validator;

class NotificationController extends Controller {

    /**
     * Notification index page.
     * Show a list of notifications for the current user.
     *
     * @return Response
     */
    public function index(Request $request) {
        // List notifications
        list($notificationsUnread, $notifications) = Notification::visible()
            // TODO: set this as default scope
            ->get()
            ->partition(function($n) {
                return $n->read_at == null;
            });
        $notificationsRead = Notification::withoutGlobalScope('visible')
            ->whereNotNull('read_at')
            ->where('persistent', 0)
            ->get();

        return view('notification.index')
            ->with('notificationsUnread', $notificationsUnread)
            ->with('notifications', $notifications)
            ->with('notificationsRead', $notificationsRead);
    }

    /**
     * Invoke a notification action.
     *
     * @return Response
     */
    public function action($notificationId, $action) {
        // Find the notification
        $notification = Notification::withoutGlobalScope('visible')
            ->findOrFail($notificationId);

        // Get the action URL
        $url = $notification->getActionUrl($action, true);
        if(is_null($url))
            return redirect()
                ->route('dashboard')
                ->with('error', __('pages.notifications.unknownNotificationAction'));

        // Redirect to the action URL
        return redirect($url);
    }
}
