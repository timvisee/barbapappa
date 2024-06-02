<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Email;
use App\Models\SessionLink;
use App\Services\Auth\AuthResult;
use Illuminate\Http\Request;

class LoginController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must not be authenticated
        $this->middleware('guest');
    }

    public function login() {
        return view('myauth.login');
    }

    public function doLogin(Request $request) {
        // Validate
        $rules = [
            'email' => 'required|' . ValidationDefaults::EMAIL,
            'password' => 'required|' . ValidationDefaults::USER_PASSWORD,
        ];
        if(is_recaptcha_enabled())
            $rules['g-recaptcha-response'] = 'required|recaptchav3:login,0.5';
        $this->validate($request, $rules);

        // Authenticate
        $result = barauth()->getAuthenticator()->authCredentials(
            $request->input('email'),
            $request->input('password')
        );

        // Show an error if the user is not authenticated
        if($result->getResult() == AuthResult::ERR_INVALID_CREDENTIALS)
            return redirect()
                ->back()
                ->with('error', __('auth.invalidCredentials'));

        // Show an error if the user is not authenticated
        if($result->isErr())
            return redirect()
                ->back()
                ->with('error', __('general.serverError'));

        // Redirect the user to the dashboard
        return redirect()
            ->intended(route('dashboard'))
            ->with('success', __('auth.loggedIn'));
    }

    public function email() {
        // Redirect to regular login if session link logins are not supported
        if(!config('app.auth_session_link'))
            return redirect()
                ->route('login');

        return view('myauth.loginEmail');
    }

    public function doEmail(Request $request) {
        // Redirect to regular login if session link logins are not supported
        if(!config('app.auth_session_link'))
            return redirect()
                ->route('login');

        // Validate
        $rules = [
            'email' => 'required|' . ValidationDefaults::EMAIL,
        ];
        if(is_recaptcha_enabled())
            $rules['g-recaptcha-response'] = 'required|recaptchav3:login,0.5';
        $this->validate($request, $rules);

        // Find a user with this email address, register if not existant
        $email = Email::where('email', $request->input('email'))->first();
        if(empty($email)) {
            add_session_error('email', __('auth.failed'));
            return redirect()
                ->route('login.email');
        }

        // Create and send session link
        SessionLink::createForMailAndSend($email);

        // Show session link sent page
        return view('myauth.loginSentSession')
            ->with('email', $email)
            ->with('loginWithPassword', $email->user->hasPassword());
    }
}
