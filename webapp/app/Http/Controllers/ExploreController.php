<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use App\Models\Community;
use Illuminate\Http\Response;

class ExploreController extends Controller {

    const PAGINATE_ITEMS = 25;

    /**
     * Community explore page.
     *
     * @return Response
     */
    public function communities() {
        $communities = Community::showExplore()->paginate(self::PAGINATE_ITEMS);

        return view('explore.community')
            ->with('communities', $communities);
    }

    /**
     * Bar explore page.
     *
     * @return Response
     */
    public function bars() {
        $bars = Bar::showExplore()->paginate(self::PAGINATE_ITEMS);

        return view('explore.bar')
            ->with('bars', $bars);
    }
}
