<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Perms\AppRoles;
use Illuminate\Http\Response;

class AppController extends Controller {

    /**
     * Application management hub.
     *
     * @return Response
     */
    public function manage() {
        // List all communities
        $communities = Community::all();

        // Show the applicatoin management page
        return view('app.manage')
            ->with('communities', $communities);
    }

    /**
     * The permission required for management/administration.
     *
     * @return PermsConfig The permission configuration.
     */
    public static function permsAdminister() {
        return AppRoles::presetAdmin();
    }
}
