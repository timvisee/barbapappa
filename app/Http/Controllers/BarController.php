<?php

namespace App\Http\Controllers;

use Validator;
use App\Helpers\ValidationDefaults;
use App\Models\Bar;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BarController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must be authenticated
        $this->middleware('auth');
    }

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
     * Bar show page.
     *
     * @return Response
     */
    public function show($barId) {
        // Get the bar and session user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        return view('bar.show')
            ->with('joined', $bar->isJoined($user));
    }

    /**
     * Bar edit page.
     *
     * @return Response
     */
    public function edit() {
        // TODO: ensure the user has permission to edit this group

        return view('bar.edit');
    }

    /**
     * Bar members page.
     *
     * @return Response
     */
    public function members() {
        // TODO: ensure the user has permission to edit this group

        return view('bar.members');
    }

    /**
     * Bar update endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function update(Request $request) {
        // TODO: ensure the user has permission to edit this group

        // Get the bar and session user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
            'slug' => 'nullable|' . ValidationDefaults::barSlug($bar),
            'password' => 'nullable|' . ValidationDefaults::SIMPLE_PASSWORD,
        ], [
            'slug.regex' => __('pages.bar.slugFieldRegexError'),
        ]);

        // Change the name properties
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
        // TODO: make sure the user has permission to join this bar

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
        // TODO: make sure the user has permission to join this bar

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
}
