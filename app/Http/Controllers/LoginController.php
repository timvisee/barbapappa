<?php

namespace App\Http\Controllers;

class LoginController extends Controller
{
    public function login() {
        return view('myauth.login');
    }

    public function doLogin() {
        return view('myauth.login');
    }
}
