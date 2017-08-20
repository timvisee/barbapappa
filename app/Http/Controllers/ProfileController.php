<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must be authenticated
        $this->middleware('auth');
    }

    public function edit() {
        // Get the session user
        $user = barauth()->getSessionUser();

        // Get the user data
        $userData = Array(
            'first_name' => $user->first_name,
            'last_name' => $user->last_name
        );

        // Show the view
        return view('profile.edit')
            ->with('userData', $userData);
    }

    public function update(Request $request) {
        // Validate
        $this->validate($request, [
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
        ]);

        // Get the user
        $user = barauth()->getSessionUser();
        if($user == null)
            // TODO: Report an error here!
            throw new \Exception('Failed to change password, unable to get session user');

        // Update the properties
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->save();

        // Redirect the user to the account overview page
        return redirect()
            ->route('account')
            ->with('success', 'Your profile has been updated.');
    }
}
