<?php

namespace App\Http\Controllers;

class PasswordController extends Controller
{
    public function request() {
        return view('myauth.password.request');
    }

    public function doRequest() {
        return view('myauth.password.request');
    }

    public function reset($token = '') {
        return view('myauth.password.reset')
            ->with('token', $token);
    }

    public function doReset() {
        return view('myauth.password.reset');
    }
}
