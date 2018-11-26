<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class DashboardController extends Controller {

    /**
     * Show the dashboard.
     *
     * @return Response Response.
     */
    public function index() {
        $user = barauth()->getSessionUser();

        // Get a list of communities and bars
        $communities = $user
            ->communities(['visited_at'], false)
            ->orderBy('pivot_visited_at', 'desc')
            ->get();
        $bars = $user
            ->bars(['visited_at'], false)
            ->orderBy('pivot_visited_at', 'desc')
            ->get();

        // Show the dashboard
        return view('dashboard')
            ->with('communities', $communities)
            ->with('bars', $bars);
    }
}
