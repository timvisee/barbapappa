<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Email;
use App\Models\SessionLink;
use Illuminate\Http\Request;

class AuthController extends Controller {

    /**
     * Login/register request from the index page.
     *
     * @param Request $request The request.
     */
    public function doContinue(Request $request) {
        // Validate
        $rules = [
            'email' => 'required|' . ValidationDefaults::EMAIL,
        ];
        if(is_recaptcha_enabled())
            $rules['g-recaptcha-response'] = 'required|recaptchav3:login,0.15';
        $this->validate($request, $rules);

        // Find a user with this email address, register if not existant
        $email = Email::where('email', $request->input('email'))->first();
        if(empty($email))
            return redirect('register')
                ->with('email', $request->input('email'))
                ->with('email_lock', true);

        // Create and send session link
        SessionLink::createForMailAndSend($email);

        // Show session link sent page
        return view('myauth.loginSentSession')
            ->with('email', $email)
            ->with('loginWithPassword', $email->user->hasPassword());
    }

    /**
     * Login a user through a session token.
     *
     * @param string $token The session link token used for authentication.
     */
    public function login(Request $request, $token) {
        // Get the user session link
        $link = SessionLink::notExpired()->where('token', $token)->first();
        if(empty($link))
            return redirect()
                ->route('index')
                ->with('error', __('auth.sessionLinkUnknown'));

        // If other session, generate code and show to user
        $is_forced = is_checked($request->input('force'));
        if(!$is_forced && !$link->isSameSession()) {
            $code = $link->newCode(false);
            return view('myauth.loginOtherSession')
                ->with('token', $token)
                ->with('code', $code);
        }

        // Show error if already authenticated
        if(barauth()->isAuth())
            return redirect()
                ->intended(route('dashboard'))
                ->with('success', __('auth.alreadyLoggedIn'));

        // Consume the authentication link
        $link->consume($token);

        // Redirect to the intended link, from session or session link
        return redirect()
            ->intended($link->intended_url ?? route('dashboard'))
            ->with('success', __('auth.loggedIn'));
    }


    /**
     * Login a user through a Laravel session and a verification code.
     *
     * @param string $code The code used to authenticate the session.
     */
    public function loginWithCode(Request $request) {
        // Show error if already authenticated
        if(barauth()->isAuth())
            return redirect()
                ->intended(route('dashboard'))
                ->with('success', __('auth.alreadyLoggedIn'));

        $code = $request->input('code');

        // Find a session
        $links = SessionLink::notExpired()->currentLaravelSession()->get();
        foreach($links as $link) {
            // The verification code must be valid
            if(!$link->isValidCode($code))
                continue;

            // Consume the authentication link
            // TODO: do not consume with token, force consume instead
            $link->consume($link->token);

            // Redirect to the intended link, from session or session link
            return redirect()
                ->intended($link->intended_url ?? route('dashboard'))
                ->with('success', __('auth.loggedIn'));
        }

        // Show error
        add_session_error('code', __('auth.verificationCodeInvalid'));
        return view('myauth.loginSentSession')
            ->with('email', null);
            // ->with('loginWithPassword', $email->user->hasPassword());
    }
}
