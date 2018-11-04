<?php

namespace App\Http\Controllers;

use Validator;
use App\Helpers\ValidationDefaults;
use App\Perms\CommunityRoles;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommunityMemberController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must be authenticated
        // TODO: define proper middleware here
        $this->middleware('auth');
    }

    /**
     * Community member index page.
     *
     * @return Response
     */
    public function index() {
        // TODO: ensure the user has permission to view the community members

        return view('community.member.index');
    }

    /**
     * Show a member of a community with the given user ID.
     *
     * @return Response
     */
    public function show($communityId, $memberId) {
        // TODO: ensure the user has permission to edit this group

        // Get the community, find the member
        $community = \Request::get('community');
        $member = $community->users()->where('user_id', $memberId)->firstOrfail();

        return view('community.member.show')
            ->with('member', $member);
    }

    /**
     * The edit page for a community member.
     *
     * @return Response
     */
    public function edit($communityId, $memberId) {
        // TODO: ensure the user has permission to edit this group

        // Get the community, find the member
        $community = \Request::get('community');
        $member = $community->users()->where('user_id', $memberId)->firstOrfail();

        // Show the edit view
        return view('community.member.edit')
            ->with('member', $member);
    }

    /**
     * Edit a community member.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $memberId) {
        // TODO: ensure the user has permission to edit this group
        // TODO: do not allow role demotion if last admin

        // Get the community, find the member
        $community = \Request::get('community');
        $member = $community->users()->where('user_id', $memberId)->firstOrfail();

        // Get the selected role, validate it
        $role = $request->input('role');
        if(!CommunityRoles::isValid($role))
            throw new \Exception("unknown role ID specified");

        // Set the role ID, save the member
        $member->pivot->role = $role;
        $member->pivot->save();

        // Redirect to the show view after editing
        return redirect()
            ->route('community.member.show', ['communityId' => $communityId, 'memberId' => $memberId])
            ->with('success', __('pages.communityMembers.memberUpdated'));
    }

    /**
     * The page to delete a community member.
     *
     * @return Response
     */
    public function delete($communityId, $memberId) {
        // TODO: ensure the user has permission to edit this group

        // Get the community, find the member
        $community = \Request::get('community');
        $member = $community->users()->where('user_id', $memberId)->firstOrfail();

        return view('community.member.delete')
            ->with('member', $member);
    }

    /**
     * Make a member leave the community.
     *
     * @return Response
     */
    public function doDelete($communityId, $memberId) {
        // TODO: ensure the user has permission to edit this group

        // Get the community, find the member
        $community = \Request::get('community');
        $member = $community->users()->where('user_id', $memberId)->firstOrfail();

        // TODO: do not allow deletion if admin
        // TODO: do not allow deletion of self

        // Delete the member
        $community->leave($member);

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.member.index', ['communityId' => $communityId])
            ->with('success', __('pages.communityMembers.memberRemoved'));
    }
}
