<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Perms\BarRoles;
use App\Perms\Builder\Builder;
use App\Perms\Builder\Config as PermsConfig;

class BarMemberController extends Controller {

    /**
     * Bar member index page.
     *
     * @return Response
     */
    public function index() {
        // Get the bar, list it's members
        $bar = \Request::get('bar');
        $members = $bar
            ->memberModels()
            ->orderBy('role', 'DESC')
            ->get();

        return view('bar.member.index')
            ->with('members', $members);
    }

    /**
     * Show a member of a bar with the given user ID.
     *
     * @return Response
     */
    public function show($barId, $memberId) {
        // Get the bar, find the member
        $bar = \Request::get('bar');
        $member = $bar->memberModels()->findOrFail($memberId);

        return view('bar.member.show')
            ->with('member', $member);
    }

    /**
     * The edit page for a bar member.
     *
     * @return Response
     */
    public function edit($barId, $memberId) {
        // Get the bar, find the member
        $bar = \Request::get('bar');
        $member = $bar->memberModels()->findOrFail($memberId);

        // Current role must be higher than user role
        $config = Builder::build()->raw(BarRoles::SCOPE, $member->role)->inherit();
        if(!perms($config))
            return redirect()
                ->route('bar.member.show', ['barId' => $barId, 'memberId' => $memberId])
                ->with('error', __('pages.barMembers.cannotEditMorePermissive'));

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
        // Get the bar, find the member
        $bar = \Request::get('bar');
        $member = $bar->memberModels()->findOrFail($memberId);
        $curRole = $member->role;
        $newRole = $request->input('role');

        // Current role must be higher than user role
        $config = Builder::build()->raw(BarRoles::SCOPE, $curRole)->inherit();
        if(!perms($config))
            return redirect()
                ->route('bar.member.show', ['barId' => $barId, 'memberId' => $memberId])
                ->with('error', __('pages.barMembers.cannotEditMorePermissive'));

        // Build validation rules, validate
        $rules = [
            'role' => 'required|' . ValidationDefaults::barRoles(),
        ];
        if($newRole != $curRole)
            $rules['confirm_role_change'] = 'accepted';
        $this->validate($request, $rules);

        // New role cannot be higher than what the user has (with inherited)
        $config = Builder::build()->raw(BarRoles::SCOPE, $newRole)->inherit();
        if(!perms($config))
            return redirect()
                ->route('bar.member.show', ['barId' => $barId, 'memberId' => $memberId])
                ->with('error', __('pages.barMembers.cannotSetMorePermissive'));

        // If manager or higher changed to lower role, and he was the last with
        // that role or higher, do not allow the change
        // TODO: allow demote if manager/admin inherited from community
        if($newRole < $curRole && $curRole > BarRoles::USER) {
            $hasOtherRanked = $bar
                ->members(['id', 'role'], true)
                ->where('bar_member.id', '<>', $memberId)
                ->where('bar_member.role', '>=', $curRole)
                ->limit(1)
                ->exists();
            if(!$hasOtherRanked)
                return redirect()
                    ->route('bar.member.show', ['barId' => $barId, 'memberId' => $memberId])
                    ->with('error', __('pages.barMembers.cannotDemoteLastManager'));
        }

        // Set the role ID, save the member
        $member->role = $newRole;
        $member->save();

        // Redirect to the show view after editing
        return redirect()
            ->route('bar.member.index', ['barId' => $barId])
            ->with('success', __('pages.barMembers.memberUpdated'));
    }

    /**
     * The page to delete a bar member.
     *
     * @return Response
     */
    public function delete($barId, $memberId) {
        // TODO: user must be community admin

        // Get the bar, find the member
        $bar = \Request::get('bar');
        $member = $bar->memberModels()->firstOrFail($memberId);

        // Do some delete checks, return on early response
        if(($return = $this->checkDelete($bar, $member)) != null)
            return $return;

        return view('bar.member.delete')
            ->with('member', $member);
    }

    /**
     * Make a member leave the bar.
     *
     * @return Response
     */
    public function doDelete(Request $request, $barId, $memberId) {
        // TODO: user must be community admin

        // Get the bar, find the member
        $bar = \Request::get('bar');
        $member = $bar->memberModels()->firstOrFail($memberId);

        // Validate confirmation when deleting authenticated member
        if($member->user_id == barauth()->getSessionUser()->id)
            $this->validate($request, ['confirm_self_delete' => 'accepted']);

        // Do some delete checks, return on early response
        if(($return = $this->checkDelete($bar, $member)) != null)
            return $return;

        // Delete the member
        $bar->leave($member->user);

        // Redirect to the index page after deleting
        return redirect()
            ->route('bar.member.index', ['barId' => $barId])
            ->with('success', __('pages.barMembers.memberRemoved'));
    }

    /**
     * Do some checks before deleting a member.
     * Extracted into a separate method to prevent duplicate code.
     *
     * @param Bar $bar The bar.
     * @param BarMember $member The bar member.
     *
     * @return null|Response Null to do nothing, or an early response.
     */
    private function checkDelete($bar, $member) {
        // Get the current role
        $curRole = $member->role;

        // Cannot delete last member with this (or higher) management role
        // TODO: allow demote if manager/admin inherited from community
        if($curRole > BarRoles::USER) {
            $hasOtherRanked = $bar
                ->memberModels()
                ->where('bar_member.id', '<>', $member->id)
                ->where('bar_member.role', '>=', $curRole)
                ->limit(1)
                ->exists();
            if(!$hasOtherRanked)
                return redirect()
                    ->route('bar.member.show', ['barId' => $bar->id, 'memberId' => $member->id])
                    ->with('error', __('pages.barMembers.cannotDeleteLastManager'));
        }
    }

    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return BarController::permsManage();
    }

    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return BarController::permsAdminister();
    }
}
