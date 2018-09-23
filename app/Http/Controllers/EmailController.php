<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Managers\EmailVerificationManager;
use App\Models\Email;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must be authenticated
        $this->middleware('auth');
    }

    /**
     * Emails page.
     *
     * @return Response
     */
    public function show() {
        return view('account.email.overview');
    }

    /**
     * Email add page.
     *
     * @return Response
     */
    public function create() {
        return view('account.email.create');
    }

    /**
     * Email add page.
     *
     * @return Response
     */
    public function doCreate($userId, Request $request) {
        // Validate
        $this->validate($request, [
            'email' => 'required|' . ValidationDefaults::EMAIL . '|unique:emails',
        ], [
            'email.unique' => __('auth.emailUsed')
        ]);

        // Get the user
        $user = \Request::get('user');

        // Create the email address
        $email = new Email();
        $email->user_id = $user->id;
        $email->email = $request->input('email');
        $email->save();

        // Make an email verification request
        EmailVerificationManager::createAndSend($email, false);

        // Redirect to the emails page, show a success message
        return redirect()
            ->route('account.emails', ['userId' => $userId])
            ->with('success', __('pages.accountPage.addEmail.added'));
    }

    /**
     * Resend a verification email.
     *
     * @return Response
     */
    public function reverify($userId, $emailId, Request $request) {
        // TODO: ensure the user has enough permission to reverify this email
        // address

        // Get the selected email address and user
        $email = Email::findOrFail($emailId);
        $user = \Request::get('user');

        // Ensure it isn't verified
        if($email->isVerified()) {
            return redirect()
                ->route('account.emails', ['userId' => $userId])
                ->with('error', __('pages.accountPage.email.alreadyVerified'));
        }

        // Make an email verification request
        EmailVerificationManager::createAndSend($email, false);

        // Redirect to the emails page, show a success message
        return redirect()
            ->route('account.emails', ['userId' => $userId])
            ->with('success', __('pages.accountPage.email.verifySent'));
    }
}
