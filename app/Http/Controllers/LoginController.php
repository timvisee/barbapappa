<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login() {
        return view('myauth.login');
    }

    public function doLogin(Request $request) {
        // Validate
        $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // TODO: Login here!

        // Redirect the user to the dashboard
        return redirect()->route('dashboard');
    }
}
