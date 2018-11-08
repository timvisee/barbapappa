<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Perms\BarRoles;
use App\Perms\Builder\Config as PermsConfig;

class BarMemberController extends Controller {

    /**
     * Bar member index page.
     *
     * @return Response
     */
    public function index() {
        return view('bar.member.index');
    }

    /**
     * Show a member of a bar with the given user ID.
     *
     * @return Response
     */
    public function show($barId, $memberId) {
        // Get the bar, find the member
        $bar = \Request::get('bar');
        $member = $bar->users(['role', 'visited_at'])->where('user_id', $memberId)->firstOrfail();

        return view('bar.member.show')
            ->with('member', $member);
    }

    /**
     * The edit page for a bar member.
     *
     * @return Response
     */
    public function edit($barId, $memberId) {
        // TODO: do not allow role demotion if last admin

        // Get the bar, find the member
        $bar = \Request::get('bar');
        $member = $bar->users(['role'])->where('user_id', $memberId)->firstOrfail();

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
        // TODO: do not allow role demotion if last admin

        // Validate
        $this->validate($request, [
            'role' => 'required|' . ValidationDefaults::barRoles(),
        ]);

        // Get the bar, find the member
        $bar = \Request::get('bar');
        $member = $bar->users(['role'], true)->where('user_id', $memberId)->firstOrfail();

        // Set the role ID, save the member
        $member->pivot->role = $request->input('role');
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
        // TODO: user must be community admin
        // TODO: do not allow role demotion if last admin

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
        // TODO: user must be community admin
        // TODO: do not allow role demotion if last admin

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

    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return BarRoles::presetManager();
    }

    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return BarRoles::presetAdmin();
    }
}
