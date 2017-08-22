<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PasswordChangeController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must be authenticated
        $this->middleware('auth');
    }

    public function change() {
        return view('myauth.password.change');
    }

    public function doChange(Request $request) {
        // Validate
        $this->validate($request, [
            'password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6|confirmed'
        ]);

        // Check whether to invalidate other sessions
        $invalidateOtherSessions = $request->input('invalidate_other_sessions') == 'true';

        // Get the user and session
        $user = barauth()->getSessionUser();
        if($user == null)
            throw new \Exception('Failed to change password, unable to get session user');

        // The current password must be valid
        if(!$user->checkPassword($request->input('password'), false))
            return redirect()
                ->back()
                ->with('error', __('auth.currentPasswordInvalid'));

        // Change the password and invalidate user sessions of others
        $user->changePassword($request->input('new_password'), true);
        $user->invalidateSessions(false, $invalidateOtherSessions);

        // Redirect the user to the account overview page
        return redirect()
            ->route('account')
            ->with('success', __('auth.passwordChanged'));
    }
}
