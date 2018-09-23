<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfileController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must be authenticated
        $this->middleware('auth');
    }

    /**
     * Profile edit page.
     *
     * @return Response
     */
    public function edit() {
        return view('profile.edit');
    }

    /**
     * Profile update endpoint.
     *
     * @param int $userId ID of the user to update the profile for.
     * @param Request $request Request.
     *
     * @return Response
     */
    public function update(Request $request) {
        // TODO: make sure the user has sufficient permissions to edit this user

        // Get the user we're editing from middleware
        $user = \Request::get('user');

        // Validate
        $this->validate($request, [
            'first_name' => 'required|' . ValidationDefaults::FIRST_NAME,
            'last_name' => 'required|' . ValidationDefaults::LAST_NAME,
        ]);

        // Change the name properties
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');

        // Get the input locale and update it if it's valid
        $locale = $request->input('language');
        if(langManager()->isValidLocale($locale))
            $user->locale = $locale;
        else if(empty($locale))
            $user->locale = null;

        // Save the user
        $user->save();

        // Change the interface locale if the changed user is the currently logged in user
        if(langManager()->isValidLocale($locale) && $user->id == barauth()->getSessionUser()->id)
            langManager()->setLocale($locale, true, false);

        // Determine whether an other user is being updated
        $isOther = $user->id != barauth()->getSessionUser()->id;

        // Redirect the user to the account overview page
        return redirect()
            ->route('account', ['userId' => $user->id])
            ->with('success', $isOther
                ? __('pages.editProfile.updated')
                : __('pages.editProfile.otherUpdated')
            );
    }
}
