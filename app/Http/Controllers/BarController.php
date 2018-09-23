<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use Illuminate\Http\Response;

class BarController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must be authenticated
        $this->middleware('auth');
    }

    /**
     * Bar overview page.
     *
     * @return Response
     */
    public function overview() {
        return view('bar.overview')
            ->with('bars', Bar::all());
    }

    /**
     * Bar show page.
     *
     * @return Response
     */
    public function show($barId) {
        return view('bar.show');
    }
}
