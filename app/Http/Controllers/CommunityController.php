<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Response;

class CommunityController extends Controller {

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        // The user must be authenticated
        $this->middleware('auth');
    }

    /**
     * Community overview page.
     *
     * @return Response
     */
    public function overview() {
        return view('community.overview')
            ->with('communities', Community::all());
    }

    /**
     * Community show page.
     *
     * @return Response
     */
    public function show($communityId) {
        // Get the community
        $community = \Request::get('community');

        return view('community.show')
            ->with('bars', $community->bars()->get());
    }
}
