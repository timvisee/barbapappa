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

        // Check whether to invalidate sessions
        $invalidateSessions = $request->input('invalidate_sessions') == 'true';

        // Reset the password with the given token
        $result = PasswordResetManager::resetPassword(
            $request->input('token'),
            $request->input('password'),
            $invalidateSessions
        );

        // If we're ok, show the success page
        if($result->isOk())
            return redirect()
                ->route('dashboard')
                ->with('success', 'Your password has been changed.');

        // TODO: Properly handle errors here!
        switch($result->getResult()) {
            case PasswordResetResult::ERR_NO_TOKEN:
                die('No token was given');
            case PasswordResetResult::ERR_INVALID_TOKEN:
                die('Invalid token');
            case PasswordResetResult::ERR_USED_TOKEN:
                die('Token already used');
            default:
                die('Server error');
        }
    }
}
