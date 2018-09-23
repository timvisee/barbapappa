<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;

class EmailController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must be authenticated
        $this->middleware('auth');
    }

    /**
     * Emails page.
     *
     * @return Response
     */
    public function show($userId = null) {
        return view('account.email.overview');
    }
}
