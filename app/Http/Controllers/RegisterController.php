<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register() {
        return view('myauth.register');
    }

    public function doRegister(Request $request) {
        // Validate
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // TODO: Register here!

        // Redirect the user to the dashboard
        return redirect()->route('dashboard');
    }
}
