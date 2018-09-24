<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class DashboardController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must be authenticated
        $this->middleware('auth');
    }

    /**
     * Show the dashboard.
     *
     * @return Response Response.
     */
    public function index() {
        $user = barauth()->getSessionUser();

        return view('dashboard')
            ->with('bars', $user->bars()->get())
            ->with('communities', $user->communities()->get());
    }
}
