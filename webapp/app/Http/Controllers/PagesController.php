<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use Illuminate\Http\Request;

class PagesController extends Controller {

    /**
     * Index page.
     *
     * @return $this
     */
    public function index() {
        // If the user is logged in, redirect to his last bar
        if(barauth()->isAuth())
            return $this->last();

        // Show the normal index page
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
        return response()
            ->file('LICENSE', ['Content-Type' => 'text/plain']);
    }

    /**
     * Last bar or community page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function last() {
        // Obtain the users last bar ID, visit the page if any was found
        $bar = Bar::find(
            \DB::table('bar_member')
                ->where('user_id', barauth()->getSessionUser()->id)
                ->whereNotNull('visited_at')
                ->latest('visited_at')
                ->pluck('bar_id')
                ->first()
        );
        if($bar != null)
            return redirect()->route('bar.show', ['barId' => $bar->human_id]);

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
    public function language(Request $request, $locale = null) {
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
                ->intended($request->query('redirect') ?? route('index'));

        } else if(!empty($locale))
            $response = $response->with('error', __('lang.unknownLanguage'));

        // Respond
        return $response;
    }
}
