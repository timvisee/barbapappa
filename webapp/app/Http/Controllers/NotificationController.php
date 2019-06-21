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
        // Get the user, community, find the products
        $user = barauth()->getUser();

        // List notifications
        list($notificationsUnread, $notifications) = Notification::visible()
            // TODO: set this as default scope
            ->latest('updated_at')
            ->get()
            ->partition(function($n) {
                return $n->read_at == null;
            });
        // TODO: saturate this list as well
        $notificationsRead = collect();

        $notificationsUnread = $notificationsUnread->concat($notificationsUnread);
        $notificationsUnread = $notificationsUnread->concat($notificationsUnread);

        return view('notification.index')
            ->with('notificationsUnread', $notificationsUnread)
            ->with('notifications', $notifications)
            ->with('notificationsRead', $notificationsRead);
    }
}
