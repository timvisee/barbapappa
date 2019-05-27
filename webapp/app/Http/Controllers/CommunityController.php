<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Community;
use App\Models\Economy;
use App\Perms\AppRoles;
use App\Perms\CommunityRoles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

// TODO: using barauth()->getSessionUser() in some places, shouldn't this be getUser() ?

class CommunityController extends Controller {

    /**
     * Community create page.
     *
     * @return Response
     */
    public function create() {
        return view('community.create');
    }

    /**
     * Create a community.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doCreate(Request $request) {
        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
            'slug' => 'nullable|' . ValidationDefaults::communitySlug(),
            'description' => 'nullable|' . ValidationDefaults::DESCRIPTION,
            'password' => 'nullable|' . ValidationDefaults::SIMPLE_PASSWORD,
        ], [
            'slug.regex' => __('pages.community.slugFieldRegexError'),
        ]);

        // Create the community
        $community = new Community();
        $community->name = $request->input('name');
        $community->slug = $request->has('slug') ? $request->input('slug') : null;
        $community->description = $request->input('description');
        $community->password = $request->has('password') ? $request->input('password') : null;
        $community->show_explore = is_checked($request->input('show_explore'));
        $community->self_enroll = is_checked($request->input('self_enroll'));
        $community->save();

        // Automatically join if checked
        if(is_checked($request->input('join')))
            $community->join(barauth()->getUser(), CommunityRoles::ADMIN);

        // Redirect the user to the community page
        return redirect()
            ->route('community.show', ['communityId' => $community->human_id])
            ->with('success', __('pages.community.created'));
    }

    /**
     * Community show page.
     *
     * @return Response
     */
    public function show($communityId) {
        // Show info page if user does not have user role
        if(!perms(Self::permsUser()))
            return $this->info($communityId);

        // Get the community and session user
        $community = \Request::get('community');
        $user = barauth()->getSessionUser();

        // Update the visit time for this member
        $member = $community->users(['visited_at'], false)
            ->where('user_id', $user->id)
            ->first();
        if($member != null) {
            $member->pivot->visited_at = new \DateTime();
            $member->pivot->save();
        }

        return view('community.show')
            ->with('joined', $community->isJoined($user))
            ->with('bars', $community->bars()->showCommunity()->get());
    }

    /**
     * Community info page.
     *
     * @return Response
     */
    public function info($communityId) {
        // Get the community and session user
        $community = \Request::get('community');
        $user = barauth()->getSessionUser();

        return view('community.info')
            ->with('joined', $community->isJoined($user))
            ->with('page', last(explode('.', \Request::route()->getName())));
    }

    /**
     * Community stats page.
     *
     * @return Response
     */
    public function stats($communityId) {
        // Get the community and session user
        $community = \Request::get('community');
        $user = barauth()->getSessionUser();

        // Gather some stats
        $memberCountHour = $community
            ->users()
            ->wherePivot('visited_at', '>=', Carbon::now()->subHour())
            ->count();
        $memberCountDay = $community
            ->users()
            ->wherePivot('visited_at', '>=', Carbon::now()->subDay())
            ->count();
        $memberCountMonth = $community
            ->users()
            ->wherePivot('visited_at', '>=', Carbon::now()->subMonth())
            ->count();

        // Show the community page
        return view('community.stats')
            ->with('memberCountHour', $memberCountHour)
            ->with('memberCountDay', $memberCountDay)
            ->with('memberCountMonth', $memberCountMonth);
    }

    /**
     * Community management hub.
     *
     * @return Response
     */
    public function manage($communityId) {
        // Get the community and session user
        $community = \Request::get('community');
        $user = barauth()->getSessionUser();

        // Show the community management page
        return view('community.manage')
            ->with('bars', $community->bars()->get());
    }

    /**
     * Community edit page.
     *
     * @return Response
     */
    public function edit() {
        return view('community.edit');
    }

    /**
     * Community update endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doEdit(Request $request) {
        // Get the community
        $community = \Request::get('community');

        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
            'slug' => 'nullable|' . ValidationDefaults::communitySlug($community),
            'description' => 'nullable|' . ValidationDefaults::DESCRIPTION,
            'password' => 'nullable|' . ValidationDefaults::SIMPLE_PASSWORD,
        ], [
            'slug.regex' => __('pages.community.slugFieldRegexError'),
        ]);

        // Change the name properties
        $community->name = $request->input('name');
        $community->slug = $request->has('slug') ? $request->input('slug') : null;
        $community->description = $request->input('description');
        $community->password = $request->has('password') ? $request->input('password') : null;
        $community->show_explore = is_checked($request->input('show_explore'));
        $community->self_enroll = is_checked($request->input('self_enroll'));

        // Save the community
        $community->save();

        // Redirect the user to the community page
        return redirect()
            ->route('community.manage', ['communityId' => $community->human_id])
            ->with('success', __('pages.community.updated'));
    }

    /**
     * The community join confirmation page.
     *
     * @return Response
     */
    public function join($communityId) {
        // Get the community and user
        $community = \Request::get('community');
        $user = barauth()->getSessionUser();

        // Redirect to the community page if the user has already joined
        if($community->isJoined($user))
            return redirect()
                ->route('community.show', ['communityId' => $communityId]);

        // Self enroll must be enabled
        if(!$community->self_enroll)
            return redirect()
                ->route('community.show', ['communityId' => $communityId])
                ->with('error', __('pages.community.cannotSelfEnroll'));

        // Show the community join confirm page
        return view('community.join');
    }

    /**
     * Make a user join the community.
     *
     * @return Response
     */
    public function doJoin(Request $request, $communityId) {
        // Get the community and user
        $community = \Request::get('community');
        $user = barauth()->getSessionUser();

        // Self enroll must be enabled
        if(!$community->self_enroll)
            return redirect()
                ->route('community.show', ['communityId' => $communityId])
                ->with('error', __('pages.community.cannotSelfEnroll'));

        // Handle the password if required
        if($community->needsPassword($user)) {
            // Validate password field input
            $this->validate($request, [
                'code' => 'required|' . ValidationDefaults::CODE,
            ]);

            // Test the password
            if(!$community->isPassword($request->input('code'))) {
                // Mark the error and retur
                $validator = Validator::make([], []);
                $validator->errors()->add('code', __('pages.community.incorrectCode'));
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Join the user
        $community->join($user);

        // Redirect to the community page
        return redirect()
            ->route('community.show', ['communityId' => $communityId])
            ->with('success', __('pages.community.joinedThisCommunity'));
    }

    /**
     * The community leave confirmation page.
     *
     * @return Response
     */
    public function leave($communityId) {
        // TODO: make sure the user can leave this community

        // Get the community and user
        $community = \Request::get('community');
        $user = barauth()->getSessionUser();

        // Redirect to the community page if the user isn't joined
        if(!$community->isJoined($user))
            return redirect()
                ->route('community.show', ['communityId' => $communityId]);

        // Show the communtiy leave confirm page
        return view('community.leave');
    }

    /**
     * Make a user leave the community.
     *
     * @return Response
     */
    public function doLeave($communityId) {
        // TODO: make sure the user can leave the community

        // Get the community and user
        $community = \Request::get('community');
        $user = barauth()->getSessionUser();

        // Leave the user
        $community->leave($user);

        // Redirect to the community page
        return redirect()
            ->route('community.show', ['communityId' => $communityId])
            ->with('success', __('pages.community.leftThisCommunity'));
    }

    /**
     * Page to delete the community.
     *
     * @return Response
     */
    public function delete($communityId) {
        // Get the community and authenticated user
        $community = \Request::get('community');
        $user = barauth()->getUser();

        // List all blockers
        $blockers = $community->getDeleteBlockers();
        if($blockers->contains(function($b) { return !($b instanceof Economy); }))
            throw new \Exception("Delete blocking entities contains unexpected types");

        return view('community.delete')
            ->with('blockingEconomies', $blockers);
    }

    /**
     * Delete the community.
     *
     * @return Response
     */
    public function doDelete(Request $request, $communityId) {
        // Get the community and authenticated user
        $community = \Request::get('community');
        $user = barauth()->getUser();

        // Validate
        $this->validate($request, [
            'confirm_name' => 'same:confirm_name_base',
            'confirm_delete' => 'accepted',
        ], [
            'confirm_name.same' => __('pages.community.incorrectNameShouldBe', ['name' => $community->name]),
        ]);

        // The community must be deletable
        if(!$community->canDelete())
            return redirect()
                ->route('community.manage', ['communityId' => $community->human_id])
                ->with('error', __('pages.community.cannotDeleteDependents'));

        // Manually delete all user wallets in this economy
        $community->wallets()->delete();

        // Delete the community
        $community->delete();

        // Redirect to the index page after deleting
        return redirect()
            ->route('dashboard')
            ->with('success', __('pages.community.deleted'));
    }

    /**
     * The permission required basic user actions.
     * @return PermsConfig The permission configuration.
     */
    public static function permsUser() {
        return CommunityRoles::presetUser();
    }

    /**
     * The permission required for basic community management.
     * This allows economy and member management.
     *
     * Editing the community itself and setting permissive user roles it not
     * allowed.
     *
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return CommunityRoles::presetManager();
    }

    /**
     * The permission required for complete community administration.
     * This allows managing anything within this community.
     *
     * @return PermsConfig The permission configuration.
     */
    public static function permsAdminister() {
        return CommunityRoles::presetAdmin();
    }

    /**
     * The permission required for creating a new community.
     *
     * @return PermsConfig The permission configuration.
     */
    public static function permsCreate() {
        return AppRoles::presetAdmin();
    }
}
