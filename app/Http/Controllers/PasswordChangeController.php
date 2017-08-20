<?php

namespace App\Http\Controllers;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Managers\PasswordResetResult;
use App\Utils\EmailRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
            // TODO: Report an error here!
            throw new \Exception('Failed to change password, unable to get session user');

        // The current password must be valid
        // TODO: Do this check somewhere else, possibly in the user model
        if(!Hash::check($request->input('password'), $user->password))
            // TODO: Show some error message
            return redirect()
                ->route('password.change')
                ->with('error', 'Your current password is invalid.');

        // Change the password
        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        // Invalidate other user sessions
        // TODO: Centralize this logic in some method as it's used on multiple changes
        if($invalidateOtherSessions)
            $user->sessions()->get()->each(function($entry) {
                // Skip if this session is the current
                if(barauth()->getAuthState()->getSession()->id == $entry->id)
                    return;

                // Invalidate the session
                $entry->invalidate();
            });

        // Get the primary email address for the user
        // TODO: Send the password reset email somewhere else
        $email = $user->getPrimaryEmail();

        // Send an additional reset token to allow the user to revert the password if the change was unwanted
        if($email != null) {
            // Create an additional reset token to allow the user to revert the password change
            $extraReset = PasswordResetManager::create($user);

            try {
                // Create a mailable
                $recipient = new EmailRecipient($email, $user);
                $mailable = new Reset($recipient, $extraReset);

                // Send the mailable
                Mail::send($mailable);

            } catch (\Exception $e) {}
        }

        // Redirect the user to the account overview page
        return redirect()
            ->route('account')
            ->with('success', 'Your password has been changed.');
    }
}
