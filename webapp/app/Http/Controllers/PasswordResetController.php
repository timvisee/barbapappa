<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Managers\PasswordResetManager;
use App\Managers\PasswordResetResult;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class PasswordResetController extends Controller {

    public function reset($token = '') {
        return view('myauth.password.reset')
            ->with('token', $token);
    }

    public function doReset(Request $request) {
        // Validate
        $this->validate($request, [
            'token' => 'required|' . ValidationDefaults::PASSWORD_RESET_TOKEN,
            'password' => 'required|' . ValidationDefaults::USER_PASSWORD . '|confirmed'
        ]);

        // Check whether to invalidate other sessions
        $invalidateOtherSessions = is_checked($request->input('invalidate_other_sessions'));

        // Reset the password with the given token
        $result = PasswordResetManager::resetPassword(
            $request->input('token'),
            $request->input('password'),
            $invalidateOtherSessions
        );

        // Create a message bag for errors
        $errorBag = new MessageBag();

        // Create a proper response
        $response = redirect();
        switch($result->getResult()) {
            case PasswordResetResult::ERR_NO_TOKEN:
                $errorBag->add('token', __('misc.noToken'));
                break;

            case PasswordResetResult::ERR_INVALID_TOKEN:
                $errorBag->add('token', __('pages.passwordReset.invalid'));
                break;

            case PasswordResetResult::ERR_USED_TOKEN:
                return $response->back()
                    ->with('error', __('pages.passwordReset.used'));

            case PasswordResetResult::ERR_EXPIRED_TOKEN:
                // TODO: Show a button/page to request a new password reset
                $errorBag->add('token', __('pages.passwordReset.expired'));
                break;

            case PasswordResetResult::OK:
                return $response->route(barauth()->isAuth() ? 'dashboard' : 'login')
                    ->with('success', __('auth.passwordChanged'));

            default:
                return $response->back()
                    ->with('error', __('general.serverError'));
        }

        // Go back with errors
        return $response->back()->withErrors($errorBag);
    }
}
