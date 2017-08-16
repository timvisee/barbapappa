<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    public function reset($token = '') {
        return view('myauth.password.reset')
            ->with('token', $token);
    }

    public function doReset(Request $request) {
        // Validate
        $this->validate($request, [
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // TODO: Send the request here!

        // Redirect the user to the dashboard
        return redirect()->route('dashboard');
    }
}
