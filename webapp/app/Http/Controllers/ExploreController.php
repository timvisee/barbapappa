<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use App\Models\Community;
use Illuminate\Http\Response;

class ExploreController extends Controller {

    /**
     * Community explore page.
     *
     * @return Response
     */
    public function communities() {
        return view('explore.community')
            ->with('communities', Community::visible()->get());
    }

    /**
     * Bar explore page.
     *
     * @return Response
     */
    public function bars() {
        return view('explore.bar')
            ->with('bars', Bar::visible()->get());
    }
}
