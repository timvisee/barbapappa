<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

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
        // Validate the inputs
        $this->validate($request, [
            'password' => 'required|' . ValidationDefaults::PASSWORD,
            'new_password' => 'required|' . ValidationDefaults::USER_PASSWORD . '|confirmed|different:password'
        ], [
            'different' => __('auth.newPasswordDifferent')
        ]);

        // Check whether to invalidate other sessions
        $invalidateOtherSessions = is_checked($request->input('invalidate_other_sessions'));

        // Get the user and session
        $user = barauth()->getSessionUser();
        if($user == null)
            throw new \Exception('Failed to change password, unable to get session user');

        // The current password must be valid
        if(!$user->checkPassword($request->input('password'), false)) {
            // Build the error bag with the error message
            $errorBag = new MessageBag();
            $errorBag->add('password', __('auth.invalidCurrentPassword'));

            // Redirect with the errors
            return redirect()
                ->back()
                ->withErrors($errorBag);
        }

        // Change the password and invalidate user sessions of others
        $user->changePassword($request->input('new_password'), true);
        $user->invalidateSessions(false, $invalidateOtherSessions);

        // Redirect the user to the account overview page
        return redirect()
            ->route('account')
            ->with('success', __('auth.passwordChanged'));
    }
}
