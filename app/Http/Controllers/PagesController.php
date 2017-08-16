<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpParser\Node\Expr\Array_;

class PagesController extends Controller
{
    //

    public function index() {
        // TODO: Build a page render wrapper
        // TODO: This wrapper should include default variables, such as the page title
        // TODO: The wrapper should also process the page parameters, and should add some default parameters
        $data = Array(
            'title' => 'Page title here',
            'auth' => barauth()->isAuth(),
            'verified' => barauth()->isVerified(),
        );

        return view('pages.index')->with($data);
    }

    public function about() {
        return view('pages.about');
    }
}
