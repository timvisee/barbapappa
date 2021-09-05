<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Wallet;
use App\Perms\Builder\Config as PermsConfig;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EconomyController extends Controller {

    const PAGINATE_ITEMS = 50;

    /**
     * Community economy index.
     *
     * @return Response
     */
    public function index() {
        return view('community.economy.index');
    }

    /**
     * Show a community economy with the given ID.
     *
     * @return Response
     */
    public function show($communityId, $economyId) {
        // Get the community, find economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currencies = $economy->currencies()->get();

        return view('community.economy.show')
            ->with('economy', $economy)
            ->with('currencies', $currencies);
    }

    /**
     * The create page for a community economy.
     *
     * @return Response
     */
    public function create($communityId) {
        return view('community.economy.create');
    }

    /**
     * Create a community economy.
     *
     * @return Response
     */
    public function doCreate(Request $request, $communityId) {
        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Get the community
        $community = \Request::get('community');

        // Create an economy and save
        $economy = $community->economies()->create([
            'name' => $request->input('name'),
        ]);

        // Redirect to the show view after creation
        return redirect()
            ->route('community.economy.currency.create', ['communityId' => $communityId, 'economyId' => $economy->id])
            ->with('success', __('pages.economies.economyCreated'));
    }

    /**
     * The edit page for a community economy.
     *
     * @return Response
     */
    public function edit($communityId, $economyId) {
        // Get the community, find the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currencies = $economy->currencies()->get();

        // Show the edit view
        return view('community.economy.edit')
            ->with('economy', $economy)
            ->with('currencies', $currencies);
    }

    /**
     * Edit a community economy.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $economyId) {
        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Get the community, find the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Update the properties
        $economy->name = $request->input('name');
        $economy->save();

        // Redirect to the show view after editing
        return redirect()
            ->route('community.economy.index', ['communityId' => $communityId])
            ->with('success', __('pages.economies.economyUpdated'));
    }

    /**
     * The page to delete a community economy.
     *
     * @return Response
     */
    public function delete($communityId, $economyId) {
        // Get the community, and the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // List all blockers
        $blockers = $economy->getDeleteBlockers();
        if($blockers->contains(function($b) { return !($b instanceof Wallet); }))
            throw new \Exception("Delete blocking entities contains unexpected types");

        return view('community.economy.delete')
            ->with('economy', $economy)
            ->with('blockingWallets', $blockers);
    }

    /**
     * Delete a community economy.
     *
     * @return Response
     */
    public function doDelete($communityId, $economyId) {
        // Get the community, find the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // The economy must be deletable
        if(!$economy->canDelete())
            return redirect()
                ->route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id])
                ->with('error', __('pages.economies.cannotDeleteDependents'));

        // Delete the economy
        $economy->delete();

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.economy.index', ['communityId' => $communityId])
            ->with('success', __('pages.economies.economyDeleted'));
    }

    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return CommunityController::permsManage();
    }

    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        // TODO: community managers should be able to change some settings?
        return CommunityController::permsAdminister();
    }
}
