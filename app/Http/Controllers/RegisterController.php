<?php

namespace App\Http\Controllers;

class RegisterController extends Controller
{
    public function register() {
        return view('myauth.register');
    }

    public function doRegister() {
        return view('myauth.register');
    }
}
