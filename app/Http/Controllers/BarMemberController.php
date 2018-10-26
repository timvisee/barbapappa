<?php

namespace App\Http\Controllers;

use Validator;
use App\Helpers\ValidationDefaults;
use App\Models\Bar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BarMemberController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must be authenticated
        // TODO: define proper middleware here
        $this->middleware('auth');
    }

    /**
     * Bar member index page.
     *
     * @return Response
     */
    public function index() {
        // TODO: ensure the user has permission to edit this group

        return view('bar.member.index');
    }

    /**
     * Show a member of a bar with the given user ID.
     *
     * @return Response
     */
    public function show($userId) {
        // TODO: ensure the user has permission to edit this group

        // // Get the bar and session user
        // $bar = \Request::get('bar');
        // $user = barauth()->getSessionUser();

        return view('bar.member.show');
    }
}
