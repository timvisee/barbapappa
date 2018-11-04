<?php

namespace App\Http\Controllers;

use Validator;
use App\Helpers\ValidationDefaults;
use App\Perms\BarRoles;

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

    /**
     * The edit page for a bar member.
     *
     * @return Response
     */
    public function edit($barId, $memberId) {
        // TODO: ensure the user has permission to edit this group

        // Get the bar, find the member
        $bar = \Request::get('bar');
        $member = $bar->users()->where('user_id', $memberId)->firstOrfail();

        // Show the edit view
        return view('bar.member.edit')
            ->with('member', $member);
    }

    /**
     * Edit a bar member.
     *
     * @return Response
     */
    public function doEdit(Request $request, $barId, $memberId) {
        // TODO: ensure the user has permission to edit this group
        // TODO: do not allow role demotion if last admin

        // Get the bar, find the member
        $bar = \Request::get('bar');
        $member = $bar->users()->where('user_id', $memberId)->firstOrfail();

        // Get the selected role, validate it
        $role = $request->input('role');
        if(!BarRoles::isValid($role))
            throw new \Exception("unknown role ID specified");

        // Set the role ID, save the member
        $member->pivot->role = $role;
        $member->pivot->save();

        // Redirect to the show view after editing
        return redirect()
            ->route('bar.member.show', ['barId' => $barId, 'memberId' => $memberId])
            ->with('success', __('pages.barMembers.memberUpdated'));
    }

    /**
     * The page to delete a bar member.
     *
     * @return Response
     */
    public function delete($barId, $memberId) {
        // TODO: ensure the user has permission to edit this group

        // Get the bar, find the member
        $bar = \Request::get('bar');
        $member = $bar->users()->where('user_id', $memberId)->firstOrfail();

        return view('bar.member.delete')
            ->with('member', $member);
    }

    /**
     * Make a member leave the bar.
     *
     * @return Response
     */
    public function doDelete($barId, $memberId) {
        // TODO: ensure the user has permission to edit this group

        // Get the bar, find the member
        $bar = \Request::get('bar');
        $member = $bar->users()->where('user_id', $memberId)->firstOrfail();

        // TODO: do not allow deletion if admin
        // TODO: do not allow deletion of self

        // Delete the member
        $bar->leave($member);

        // Redirect to the index page after deleting
        return redirect()
            ->route('bar.member.index', ['barId' => $barId])
            ->with('success', __('pages.barMembers.memberRemoved'));
    }
}
