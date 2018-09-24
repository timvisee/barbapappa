<?php

namespace App\Http\Controllers;

use App\Models\Bar;
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
            ->with('bars', Bar::all());
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
    public function doJoin($barId) {
        // TODO: make sure the user has permission to join this bar

        // Get the bar and user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

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
