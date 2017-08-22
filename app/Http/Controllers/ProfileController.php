<?php

namespace App\Http\Controllers;

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
     * @param int $userId ID of the user to edit the profile for.
     *
     * @return Response
     */
    public function edit($userId) {
        // Get the user
        /** @var User $user */
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $user = User::findOrFail($userId);

        // TODO: Make sure the current user has permission to edit the given user

        // Show the view
        return view('profile.edit')
            ->with('user', $user);
    }

    /**
     * Profile update endpoint.
     *
     * @param int $userId ID of the user to update the profile for.
     * @param Request $request Request.
     *
     * @return Response
     */
    public function update($userId, Request $request) {
        // Get the user
        /** @var User $user */
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $user = User::findOrFail($userId);

        // TODO: Make sure the current user has permission to edit the given user

        // Validate
        $this->validate($request, [
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
        ]);

        // Change the name properties
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');

        // Get the input locale and update it if it's valid
        $locale = $request->input('locale');
        if(langManager()->isValidLocale($locale))
            $user->locale = $locale;
        else if(empty($locale))
            $user->locale = null;

        // Save the user
        $user->save();

        // Change the interface locale if the changed user is the currently logged in user
        if(langManager()->isValidLocale($locale) && $user->id == barauth()->getSessionUser()->id)
            langManager()->setLocale($locale, true, false);

        // Redirect the user to the account overview page
        return redirect()
            ->route('account.show', ['userId' => $user->id])
            ->with('success', 'Your/the profile has been updated.');
    }
}
