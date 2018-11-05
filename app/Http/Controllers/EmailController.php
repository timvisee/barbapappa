<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Managers\EmailVerificationManager;
use App\Models\Email;
use App\Models\User;
use App\Perms\AppRoles;

class EmailController extends Controller {

    /**
     * Emails page.
     *
     * @param Request $request The request.
     * @param string $userId The user ID.
     *
     * @return Response
     */
    public function show(Request $request, $userId) {
        // To edit a different user, ensure we have administrator privileges
        if(barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

        return view('account.email.overview');
    }

    /**
     * Email add page.
     *
     * @param Request $request The request.
     * @param string $userId The user ID.
     *
     * @return Response
     */
    public function create(Request $request, $userId) {
        // To edit a different user, ensure we have administrator privileges
        if(barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

        return view('account.email.create');
    }

    /**
     * Email add page.
     *
     * @param Request $request The request.
     * @param string $userId The user ID.
     *
     * @return Response
     */
    public function doCreate(Request $request, $userId) {
        // To edit a different user, ensure we have administrator privileges
        if(barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

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
     * @param Request $request The request.
     * @param string $userId The user ID.
     * @param string $emailId The email ID.
     *
     * @return Response
     */
    public function reverify(Request $request, $userId, $emailId) {
        // To edit a different user, ensure we have administrator privileges
        if(barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

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
     * The email address delete confirmation page.
     *
     * @param Request $request The request.
     * @param string $userId The user ID.
     * @param string $emailId The email ID.
     *
     * @return Response
     */
    public function delete($userId, $emailId) {
        // To edit a different user, ensure we have administrator privileges
        if(barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

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

        // Show the delete confirm page
        return view('account.email.delete')
            ->with('email', $email);
    }

    /**
     * Do delete an email address.
     *
     * @param Request $request The request.
     * @param string $userId The user ID.
     * @param string $emailId The email ID.
     *
     * @return Response
     */
    public function doDelete(Request $request, $userId, $emailId) {
        // To edit a different user, ensure we have administrator privileges
        if(barauth()->getSessionUser()->id != $userId && !perms(AppRoles::presetAdmin()))
            return response(view('noPermission'));

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

        // Delete the email address
        $email->delete();

        // Redirect to the emails page, show a success message
        return redirect()
            ->route('account.emails', ['userId' => $userId])
            ->with('success', __('pages.accountPage.email.deleted'));
    }
}
