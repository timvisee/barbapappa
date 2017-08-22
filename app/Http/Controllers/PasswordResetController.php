<?php

namespace App\Http\Controllers;

use App\Managers\PasswordResetManager;
use App\Managers\PasswordResetResult;
use Illuminate\Http\Request;

class PasswordResetController extends Controller {

    public function reset($token = '') {
        return view('myauth.password.reset')
            ->with('token', $token);
    }

    public function doReset(Request $request) {
        // Validate
        $this->validate($request, [
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed'
        ]);

        // Check whether to invalidate other sessions
        $invalidateOtherSessions = $request->input('invalidate_other_sessions') == 'true';

        // Reset the password with the given token
        $result = PasswordResetManager::resetPassword(
            $request->input('token'),
            $request->input('password'),
            $invalidateOtherSessions
        );

        // Create a proper response
        $response = redirect();
        switch($result->getResult()) {
            case PasswordResetResult::ERR_NO_TOKEN:
                return $response->back()
                    ->with('error', __('misc.noToken'));

            case PasswordResetResult::ERR_INVALID_TOKEN:
                return $response->back()
                    ->with('error', __('pages.passwordReset.invalid'));

            case PasswordResetResult::ERR_USED_TOKEN:
                return $response->back()
                    ->with('error', __('pages.passwordReset.used'));

            case PasswordResetResult::ERR_EXPIRED_TOKEN:
                // TODO: Show a page to request a new password reset
                return $response->back()
                    ->with('error', __('pages.passwordReset.expired'));

            case PasswordResetResult::OK:
                return $response->route('login')
                    ->with('success', __('auth.passwordReset.changed'));

            default:
                return $response->back()
                    ->with('error', __('general.serverError'));
        }
    }
}
