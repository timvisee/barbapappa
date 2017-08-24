<?php

namespace App\Http\Controllers;

class PagesController extends Controller {

    /**
     * Index page.
     *
     * @return $this
     */
    public function index() {
        return view('pages.index');
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

    /**
     * License page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function license() {
        return view('pages.license');
    }

    /**
     * Language selection page.
     *
     * @param string|null $locale The selected locale.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function language($locale = null) {
        // TODO: Redirect to the page we were going to!

        // Get the language manager
        $langManager = langManager();

        // Create the response
        $response = view('pages.language');

        // Set the locale if valid
        if($langManager->isValidLocale($locale)) {
            // Set the locale
            $langManager->setLocale($locale, true, true);

            // Redirect to the dashboard
            return redirect()
                ->route('index')
                ->with('success', __('lang.selectedLanguage'));

        } else if(!empty($locale))
            $response = $response->with('error', __('lang.unknownLanguage'));

        // Respond
        return $response;
    }
}
