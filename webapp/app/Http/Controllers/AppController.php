<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Community;
use App\Models\Economy;
use App\Perms\AppRoles;
use App\Perms\CommunityRoles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Validator;

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
