<?php

namespace App\Http\Controllers;

use App\Managers\PasswordResetManager;
use App\Models\Email;
use Illuminate\Http\Request;

class PasswordForgetController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must not be authenticated
        $this->middleware('guest');
    }

    public function request() {
        return view('myauth.password.request');
    }

    public function doRequest(Request $request) {
        // Validate
        $this->validate($request, [
            'email' => 'required|string|email|max:255',
        ]);

        // Get the email address
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $email = Email::where('email', '=', $request->input('email'))->first();
        if($email == null)
            // If the email address is unknown, also show the reset page
            return view('myauth.password.requestSent');

        // Create and send a password reset token to the user
        PasswordResetManager::createAndSend($email);

        // Show a success page
        return view('myauth.password.requestSent');
    }
}
