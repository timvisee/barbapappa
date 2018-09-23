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
        //$user = barauth()-getUser();

        return view('dashboard')
            ->with('bars', [])
            ->with('communities', []);
    }
}
