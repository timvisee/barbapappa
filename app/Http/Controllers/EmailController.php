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

    /**
     * Delete an email address.
     *
     * @return Response
     */
    public function delete($userId, $emailId, Request $request) {
        // TODO: ensure the user has enough permission to delete this email
        // address

        // Get the selected email address and user
        $email = Email::findOrFail($emailId);
        $user = \Request::get('user');

        // Count the number of verified e-mail addresses left after this
        // deletion
        $verifiedAfter = $user
            ->emails()
            ->verified()
            ->where('id', '!=', $email->id)
            ->count();

        // Ensure there are enough verified email addresses left
        if($verifiedAfter <= 0) {
            return redirect()
                ->route('account.emails', ['userId' => $userId])
                ->with('error', __('pages.accountPage.email.cannotDeleteMustHaveVerified'));
        }

        // Delete the email address and it's verification tokens
        // TODO: don't explicitly delete verifications, cascade through SQL
        $email->verifications()->delete();
        $email->delete();

        // Redirect to the emails page, show a success message
        return redirect()
            ->route('account.emails', ['userId' => $userId])
            ->with('success', __('pages.accountPage.email.deleted'));
    }
}
