<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PasswordForgetController extends Controller
{
    public function request() {
        return view('myauth.password.request');
    }

    public function doRequest(Request $request) {
        // Validate
        $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        // TODO: Send the request here!

        // Redirect the user to the dashboard
        return redirect()->route('dashboard');
    }
}
