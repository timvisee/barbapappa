<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Validator;

use App\Helpers\ValidationDefaults;

class BalanceImportSystemController extends Controller {

    /**
     * Balance import system index page.
     * This shows a list of registered balance import systems for this economy.
     *
     * @return Response
     */
    public function index(Request $request, $communityId, $economyId) {
        // Get the user, community, find the systems
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $systems = $economy->BalanceImportSystems;

        return view('community.economy.balanceimport.index')
            ->with('economy', $economy)
            ->with('systems', $systems);
    }

    /**
     * Balance import system creation page.
     *
     * @return Response
     */
    public function create(Request $request, $communityId, $economyId) {
        // Get the user, community
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        return view('community.economy.balanceimport.create')
            ->with('economy', $economy);
    }

    /**
     * Balance import system create endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doCreate(Request $request, $communityId, $economyId) {
        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Get the user, community
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Create the balance import system
        $system = $economy->BalanceImportSystems()->create([
            'name' => $request->input('name'),
        ]);

        // Redirect to the show view after creation
        return redirect()
            ->route('community.economy.balanceimport.show', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
                'systemId' => $system->id,
            ])
            ->with('success', __('pages.balanceImport.created'));
    }

    /**
     * Show a balance import system.
     *
     * @return Response
     */
    public function show($communityId, $economyId, $systemId) {
        // Get the user, community, find the system
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);

        return view('community.economy.balanceimport.show')
            ->with('economy', $economy)
            ->with('system', $system);
    }

    /**
     * Edit a balance import system.
     *
     * @return Response
     */
    public function edit($communityId, $economyId, $systemId) {
        // Get the user, community, find the system
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);

        return view('community.economy.balanceimport.edit')
            ->with('economy', $economy)
            ->with('system', $system);
    }

    /**
     * Balance import system update endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $economyId, $systemId) {
        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Get the user, community, find the system
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);

        // Update the properties
        $system->name = $request->input('name');
        $system->save();

        // Redirect to the show view
        return redirect()
            ->route('community.economy.balanceimport.show', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
                'systemId' => $system->id,
            ])
            ->with('success', __('pages.balanceImport.changed'));
    }

    /**
     * Page for confirming the deletion of balance import system.
     *
     * @return Response
     */
    public function delete($communityId, $economyId, $systemId) {
        // Get the user, community, economy, find the system
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);

        // Do not allow deleting if there's any event
        if($system->events()->limit(1)->count() > 0)
            return redirect()
                ->route('community.economy.balanceimport.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                ])
                ->with('error', __('pages.balanceImport.cannotDeleteHasEvents'));

        return view('community.economy.balanceimport.delete')
            ->with('economy', $economy)
            ->with('system', $system);
    }

    /**
     * Delete the balance import system.
     *
     * @return Response
     */
    public function doDelete(Request $request, $communityId, $economyId, $systemId) {
        // Get the user, community, economy, find the system
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);

        // Do not allow deleting if there's any event
        if($system->events()->limit(1)->count() > 0)
            return redirect()
                ->route('community.economy.balanceimport.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                ])
                ->with('error', __('pages.balanceImport.cannotDeleteHasEvents'));

        // Delete the system
        $system->delete();

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.economy.balanceimport.index', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
            ])
            ->with('success', __('pages.balanceImport.deleted'));
    }

    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return EconomyController::permsView();
    }

    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return EconomyController::permsManage();
    }
}
