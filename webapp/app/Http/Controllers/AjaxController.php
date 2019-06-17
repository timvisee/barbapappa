<?php

namespace App\Http\Controllers;

use App\Models\Notifications\Notification;
use Illuminate\Http\Response;

class AjaxController extends Controller {

    /**
     * Get the messages sidebar content.
     *
     * @return Response Response.
     */
    public function messagesSidebar() {
        // Build the response
        $response = view('ajax.sidebarMessages');

        // List notifications
        if(barauth()->isAuth()) {
            list($notificationsUnread, $notifications) = Notification::visible()
                ->get()
                ->partition(function($n) {
                    return $n->read_at == null;
                });
            $response = $response
                ->with('notificationsUnread', $notificationsUnread)
                ->with('notifications', $notifications);

            // Mark all as read for now
            $notificationsUnread->each(function($m) {
                $m->markAsRead();
            });
        }

        return $response;
    }
}
