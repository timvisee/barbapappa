<?php

namespace App\Http\Controllers;

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
        $this->validate($request, [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        // Authenticate
        $result = barauth()->getAuthenticator()->authCredentials(
            $request->input('email'),
            $request->input('password')
        );

        // Show an error if the user is not authenticated
        if($result->getResult() == AuthResult::ERR_INVALID_CREDENTIALS) {
            die('Invalid credentials!');
        }

        // Show an error if the user is not authenticated
        if($result->isErr()) {
            die('Other error occurred!');
        }

        // Redirect the user to the dashboard
        return redirect()->route('dashboard');
    }
}
