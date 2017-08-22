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

        // Create the verification and send the email if an email address is found
        if($email != null)
            PasswordResetManager::createAndSend($email);

        // Show a success page (even if the email address was unknown)
        return view('myauth.password.requestSent')
            ->with('success');
    }
}
