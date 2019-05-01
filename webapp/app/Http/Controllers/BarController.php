<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Models\Bar;
use App\Perms\BarRoles;

class BarController extends Controller {

    /**
     * Bar overview page.
     *
     * @return Response
     */
    public function overview() {
        return view('bar.overview')
            ->with('bars', Bar::visible()->get());
    }

    /**
     * Bar creation page.
     *
     * @return Response
     */
    public function create() {
        // Get the community
        $community = \Request::get('community');

        // An economy must be created first if not available
        if($community->economies()->count() == 0) {
            // TODO: redirect the user back to this page after economy creation
            return redirect()
                ->route('community.economy.create', ['communityId' => $community->id])
                ->with('error', __('pages.bar.mustCreateEconomyFirst'));
        }

        return view('bar.create');
    }

    /**
     * Bar create page.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doCreate(Request $request) {
        // Get the community
        $community = \Request::get('community');

        // Validate
        $this->validate($request, [
            'economy' => ['required', ValidationDefaults::communityEconomy($community)],
            'name' => 'required|' . ValidationDefaults::NAME,
            'slug' => 'nullable|' . ValidationDefaults::barSlug(),
            'password' => 'nullable|' . ValidationDefaults::SIMPLE_PASSWORD,
        ], [
            'slug.regex' => __('pages.bar.slugFieldRegexError'),
        ]);

        // Create the bar
        $bar = $community->bars()->create([
            'economy_id' => $request->input('economy'),
            'name' => $request->input('name'),
            'slug' => $request->has('slug') ? $request->input('slug') : null,
            'password' => $request->has('password') ? $request->input('password') : null,
            'visible' => is_checked($request->input('visible')),
            'public' => is_checked($request->input('public')),
        ]);

        // Redirect the user to the account overview page
        return redirect()
            ->route('bar.show', ['barId' => $bar->human_id])
            ->with('success', __('pages.bar.created'));
    }

    /**
     * Bar show page.
     *
     * @return Response
     */
    public function show($barId) {
        // Get the bar and session user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Update the visit time for this member
        $member = $bar->users(['visited_at'], true)
            ->where('user_id', $user->id)
            ->first();
        if($member != null) {
            $member->pivot->visited_at = new \DateTime();
            $member->pivot->save();
        }

        // Build a list of products
        $products = [];

        // Search, or show top products
        $search = \Request::get('q');
        if(!empty($search))
            $products = $bar->searchProducts($search);
        else
            $products = $bar->quickBuyProducts();

        // Show the bar page
        return view('bar.show')
            ->with('economy', $bar->economy)
            ->with('joined', $bar->isJoined($user))
            ->with('products', $products);
    }

    /**
     * Bar edit page.
     *
     * @return Response
     */
    public function edit() {
        return view('bar.edit');
    }

    /**
     * Bar update endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function update(Request $request) {
        // Get the community, bar and session user
        $community = \Request::get('community');
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Validate
        $this->validate($request, [
            'economy' => ['required', ValidationDefaults::communityEconomy($community)],
            'name' => 'required|' . ValidationDefaults::NAME,
            'slug' => 'nullable|' . ValidationDefaults::barSlug($bar),
            'password' => 'nullable|' . ValidationDefaults::SIMPLE_PASSWORD,
        ], [
            'slug.regex' => __('pages.bar.slugFieldRegexError'),
        ]);

        // Change the name properties
        $bar->economy_id = $request->input('economy');
        $bar->name = $request->input('name');
        $bar->slug = $request->has('slug') ? $request->input('slug') : null;
        $bar->password = $request->has('password') ? $request->input('password') : null;
        $bar->visible = is_checked($request->input('visible'));
        $bar->public = is_checked($request->input('public'));

        // Save the bar
        $bar->save();

        // Redirect the user to the account overview page
        return redirect()
            ->route('bar.show', ['barId' => $bar->human_id])
            ->with('success', __('pages.bar.updated'));
    }

    /**
     * The bar join confirmation page.
     *
     * @return Response
     */
    public function join($barId) {
        // Get the bar and user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Redirect to the bar page if the user has already joined
        if($bar->isJoined($user))
            return redirect()
                ->route('bar.show', ['barId' => $barId]);

        // Redirect to the bar page
        return view('bar.join');
    }

    /**
     * Make a user join the bar.
     *
     * @return Response
     */
    public function doJoin(Request $request, $barId) {
        // Get the bar, community and user
        $bar = \Request::get('bar');
        $community = \Request::get('community');
        $user = barauth()->getSessionUser();

        // Handle the password if required
        if($bar->needsPassword($user)) {
            // Validate password field input
            $this->validate($request, [
                'code' => 'required|' . ValidationDefaults::CODE,
            ]);

            // Test the password
            if(!$bar->isPassword($request->input('code'))) {
                // Mark the error and retur
                $validator = Validator::make([], []);
                $validator->errors()->add('code', __('pages.bar.incorrectCode'));
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Join the community
        if(!$community->isJoined($user)) {
            // TODO: ensure the user has permission to join this community

            // Check whether to join their community
            $joinCommunity = is_checked($request->input('join_community'));

            // Join the community
            if($joinCommunity)
                $community->join($user);
        }

        // Join the user
        $bar->join($user);

        // Redirect to the bar page
        return redirect()
            ->route('bar.show', ['barId' => $barId])
            ->with('success', __('pages.bar.joinedThisBar'));
    }

    /**
     * The bar leave confirmation page.
     *
     * @return Response
     */
    public function leave($barId) {
        // TODO: make sure the user can leave this bar

        // Get the bar and user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Redirect to the bar page if the user isn't joined
        if(!$bar->isJoined($user))
            return redirect()
                ->route('bar.show', ['barId' => $barId]);

        // Redirect to the bar page
        return view('bar.leave');
    }

    /**
     * Make a user leave the bar.
     *
     * @return Response
     */
    public function doLeave($barId) {
        // TODO: make sure the user can leave the bar

        // Get the bar and user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Leave the user
        $bar->leave($user);

        // Redirect to the bar page
        return redirect()
            ->route('bar.show', ['barId' => $barId])
            ->with('success', __('pages.bar.leftThisBar'));
    }

    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return BarRoles::presetAdmin();
    }

    /**
     * The permission required creating a new bar.
     * @return PermsConfig The permission configuration.
     */
    public static function permsCreate() {
        return CommunityController::permsManage();
    }
}
