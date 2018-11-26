<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Managers\EmailVerificationManager;
use App\Managers\EmailVerifyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class EmailVerifyController extends Controller {

    /**
     * Verify the given token.
     *
     * @param string|null $token The token to verify.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function verify($token = '') {
        // If no token is given, show the view to enter a token
        if(empty($token))
            return view('email.verify');

        // Verify the token and create a response
        return $this->verifyToken($token);
    }

    /**
     * Verify the given token.
     *
     * @param Request $request Request.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function doVerify(Request $request) {
        // Validate
        $this->validate($request, [
            'token' => 'required|string'
        ]);

        // Verify the token and create a response
        return $this->verifyToken($request->input('token'));
    }

    /**
     * Verify the given token and return the proper following view.
     *
     * @param string $token Token to verify.
     * @return \Illuminate\Http\RedirectResponse Response.
     */
    private function verifyToken($token) {
        // Validate
        $validator = Validator::make([
            'token' => $token
        ], [
            'token' => 'required|' . ValidationDefaults::EMAIL_VERIFY_TOKEN,
        ]);

        // Revisit the page if we fail
        if($validator->fails())
            return redirect()
                ->route('email.verify')
                ->withErrors($validator);

        // Create a message bag for errors
        $errorBag = new MessageBag();

        // Verify the given token
        $result = EmailVerificationManager::verifyToken($token);

        // Build the response
        $response = redirect();
        switch($result->getResult()) {
            case EmailVerifyResult::ERR_NO_TOKEN:
                $errorBag->add('token', __('misc.noToken'));
                break;

            case EmailVerifyResult::ERR_INVALID_TOKEN:
                $errorBag->add('token', __('pages.verifyEmail.invalid'));
                break;

            case EmailVerifyResult::ERR_EXPIRED_TOKEN:
                // TODO: Show a page to allow requesting a new verification email.
                $errorBag->add('token', __('pages.verifyEmail.expired'));
                break;

            case EmailVerifyResult::ERR_ALREADY_VERIFIED:
                return $response
                    ->route(barauth()->isAuth() ? 'dashboard' : 'index')
                    ->with('success', __('pages.verifyEmail.alreadyVerified'));

            case EmailVerifyResult::OK:
                return $response
                    ->route(barauth()->isAuth() ? 'dashboard' : 'index')
                    ->with('success', __('pages.verifyEmail.verified'));

            default:
                return $response
                    ->route('email.verify')
                    ->with('error', __('general.serverError'));
        }

        // Route to the verification page and show errors
        return $response->route('email.verify')
            ->withErrors($errorBag);
    }
}
