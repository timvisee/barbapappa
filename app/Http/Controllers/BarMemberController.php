<?php

namespace App\Http\Controllers;

use Validator;
use App\Helpers\ValidationDefaults;

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
    public function show($barId, $memberId) {
        // TODO: ensure the user has permission to edit this group

        // Get the bar, find the member
        $bar = \Request::get('bar');
        $member = $bar->users()->where('user_id', $memberId)->firstOrfail();

        return view('bar.member.show')
            ->with('member', $member);
    }
}
