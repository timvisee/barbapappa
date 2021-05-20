<?php

namespace App\Http\Controllers;

use App\Models\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller {

    /**
     * Notification index page.
     * Show a list of notifications for the current user.
     *
     * @return Response
     */
    public function index(Request $request) {
        // List notifications
        list($notificationsUnread, $notifications) = Notification::get()
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
     * Mark all notifications as read for the current user.
     *
     * @return Response
     */
    public function doMarkAllRead() {
        // Mark all notifications as read
        $notifications = Notification::unread()
            ->select(['id', 'read_at'])
            ->get();
        $notifications->each(function($notification) {
            $notification->markAsRead();
        });

        // Redirect back to notification overview page
        return redirect()
            ->route('notification.index')
            ->with('success', trans_choice('pages.notifications.markedAsRead#', $notifications->count()));
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
