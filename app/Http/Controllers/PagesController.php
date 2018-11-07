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
     * Raw license page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function licenseRaw() {
        $response = \Response::make(\View::make('pages.includes.raw.license'), 200);
        $response->header('Content-Type', 'text/plain');
        return $response;
    }

    /**
     * Last bar or community page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function last() {
        // Obtain the users last bar ID, visit the page if any was found
        $barId = \DB::table('bar_user')
            ->where('user_id', barauth()->getSessionUser()->id)
            ->whereNotNull('visited_at')
            ->orderBy('visited_at', 'desc')
            ->pluck('bar_id')
            ->first();
        if($barId !== null)
            return redirect()->route('bar.show', ['barId' => $barId]);

        // No last bar, visit the dashboard and tell the user
        return redirect()
            ->route('dashboard')
            ->with('info', __('pages.last.noLast'));
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
