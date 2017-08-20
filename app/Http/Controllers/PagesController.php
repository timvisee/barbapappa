<?php

namespace App\Http\Controllers;

class PagesController extends Controller {

    /**
     * Index page.
     *
     * @return $this
     */
    public function index() {
        // TODO: Build a page render wrapper
        // TODO: This wrapper should include default variables, such as the page title
        // TODO: The wrapper should also process the page parameters, and should add some default parameters
        $data = Array(
            'title' => 'Page title here',
            'auth' => barauth()->isAuth(),
            'verified' => barauth()->isVerified(),
        );

        return view('pages.index')->with($data);
    }

    /**
     * About page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function about() {
        return view('pages.about');
    }

    /**
     * Contact page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contact() {
        return view('pages.contact');
    }

    /**
     * Terms page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function terms() {
        return view('pages.terms');
    }

    /**
     * Privacy page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function privacy() {
        return view('pages.privacy');
    }
}
