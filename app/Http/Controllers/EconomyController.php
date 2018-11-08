<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Perms\CommunityRoles;
use App\Perms\Builder\Config as PermsConfig;

class EconomyController extends Controller {

    /**
     * Community economy index.
     *
     * @return Response
     */
    public function index() {
        return view('community.economy.index');
    }

    /**
     * Show a community economy with the given ID.
     *
     * @return Response
     */
    public function show($communityId, $economyId) {
        // Get the community, find economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        return view('community.economy.show')
            ->with('economy', $economy);
    }

    /**
     * The page to delete a community economy.
     *
     * @return Response
     */
    public function delete($communityId, $economyId) {
        // Get the community, and the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        return view('community.economy.delete')
            ->with('economy', $economy);
    }

    /**
     * Delete a community economy.
     *
     * @return Response
     */
    public function doDelete($communityId, $economyId) {
        // Get the community, find the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // TODO: ensure deletion is allowed (and no users are using it)

        // Delete the economy
        $economy->delete();

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.economy.index', ['communityId' => $communityId])
            ->with('success', __('pages.economies.economyDeleted'));
    }

    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return CommunityRoles::presetManager();
    }

    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return CommunityRoles::presetAdmin();
    }










    /**
     * The edit page for a community member.
     *
     * @return Response
     */
    public function edit($communityId, $memberId) {
        // TODO: do not allow role demotion if last admin

        // Get the community, find the member
        $community = \Request::get('community');
        $member = $community->users(['role'])->where('user_id', $memberId)->firstOrfail();

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
        // TODO: do not allow role demotion if last admin

        // Get the community, find the member
        $community = \Request::get('community');
        $member = $community->users(['role'], true)->where('user_id', $memberId)->firstOrfail();

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
}
