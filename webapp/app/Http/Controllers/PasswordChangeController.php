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
        // Determine whether the user can disable his password
        $can_disable = config('app.auth_session_link')
            && barauth()->getUser()->hasPassword();

        return view('myauth.password.change')
            ->with('has_password', barauth()->getUser()->hasPassword())
            ->with('can_disable', $can_disable);
    }

    public function doChange(Request $request) {
        // Determine whether to require the users current password
        $has_password = barauth()->getUser()->hasPassword();

        // Validate the inputs
        $rules = [
            'new_password' => 'required|' . ValidationDefaults::USER_PASSWORD . '|confirmed'
        ];
        if($has_password) {
            $rules['password'] = 'required|' . ValidationDefaults::USER_PASSWORD;
            $rules['new_password'] = $rules['new_password'] . '|different:password';
        }
        $this->validate($request, $rules, [
            'different' => __('auth.newPasswordDifferent')
        ]);

        // Check whether to invalidate other sessions
        $invalidateOtherSessions = is_checked($request->input('invalidate_other_sessions'));

        // Get the user and session
        $user = barauth()->getSessionUser();
        if($user == null)
            throw new \Exception('Failed to change password, unable to get session user');

        // The current password must be valid
        if($has_password && !$user->checkPassword($request->input('password'), false)) {
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

    public function disable() {
        // Do not allow disabling if session links are not enabled
        $can_disable = config('app.auth_session_link')
            && barauth()->getUser()->hasPassword();
        if(!$can_disable)
            return redirect()
                ->route('password.change');

        return view('myauth.password.disable');
    }

    public function doDisable(Request $request) {
        // Do not allow disabling if session links are not enabled
        $can_disable = config('app.auth_session_link')
            && barauth()->getUser()->hasPassword();
        if(!$can_disable)
            return redirect()
                ->route('password.change');

        // Validate the inputs
        $this->validate($request, [
            'password' => 'required|' . ValidationDefaults::USER_PASSWORD,
        ]);

        // Check whether to invalidate other sessions
        $invalidateOtherSessions = is_checked($request->input('invalidate_other_sessions'));

        // Get the user and session
        $user = barauth()->getSessionUser();
        if($user == null)
            throw new \Exception('Failed to disable password, unable to get session user');

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

        // Disable the password and invalidate user sessions of others
        $user->disablePassword(true);
        $user->invalidateSessions(false, $invalidateOtherSessions);

        // Redirect the user to the account overview page
        return redirect()
            ->route('account')
            ->with('success', __('auth.passwordDisabled'));
    }
}
