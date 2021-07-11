<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Jobs\BalanceImportSystemMailUpdates;
use App\Models\BalanceImportAlias;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BalanceImportSystemController extends Controller {

    /**
     * Balance import system index page.
     * This shows a list of registered balance import systems for this economy.
     *
     * @return Response
     */
    public function index(Request $request, $communityId, $economyId) {
        // Get the community, economy and systems
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
        // Get the community and economy
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

        // Get the community and economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Create the balance import system
        $system = $economy->BalanceImportSystems()->create([
            'name' => $request->input('name'),
        ]);

        // Redirect to the show view after creation
        return redirect()
            ->route('community.economy.balanceimport.event.index', [
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
        // Get the community, economy, find the system, list events
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
        // Get the community, economy, find the system
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

        // Get the community, economy, find the system
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
        // Get the community, economy, find the system
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
        // Get the community, economy, find the system
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
     * Export a list of user alias addresses of users that have committed
     * balance import changes.
     *
     * @return Response
     */
    public function exportUserList($communityId, $economyId, $systemId) {
        // Get the community, economy, find the system
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);

        // Build email data for users that have committed balance import changes
        $aliases = $system
            ->changes()
            ->committed()
            ->distinct('alias_id')
            ->pluck('alias_id');
        $data = BalanceImportAlias::whereIn('id', $aliases)
            ->pluck('email')
            ->join("\r\n");

        return view('community.economy.balanceimport.exportUserList')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('data', $data);
    }

    /**
     * Page to send a balance update mail with.
     *
     * @return Response
     */
    public function mailBalance($communityId, $economyId, $systemId) {
        // Get the community, economy, find the system
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $last_event = $system->events()->latest()->first();

        // TODO: there must be an event to send updates for

        return view('community.economy.balanceimport.mailBalance')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('last_event', $last_event);
    }

    /**
     * Do send balance update mail.
     *
     * @return Response
     */
    public function doMailBalance(Request $request, $communityId, $economyId, $systemId) {
        // Get the community, economy, find the system
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $last_event = $system->events()->latest()->first();

        // Validate
        $this->validate($request, [
            'message' => 'nullable|string',
            'invite_to_bar' => 'integer|prohibited_if:related_bar,0',
            'reply_to' => 'nullable|' . ValidationDefaults::EMAIL,
            'confirm_send_mail' => 'accepted',
        ], [
            'invite_to_bar.prohibited_if' => __('pages.balanceImportMailBalance.mustSelectBarToInvite'),
        ]);

        // Read input fields
        $mail_unregistered_users = is_checked($request->input('mail_unregistered_users'));
        $mail_not_joined_users = is_checked($request->input('mail_not_joined_users'));
        $mail_joined_users = is_checked($request->input('mail_joined_users'));
        $limit_last_event = is_checked($request->input('limit_last_event'));
        $message = $request->input('message');
        $related_bar_id = (int) $request->input('related_bar');
        $invite_to_bar = is_checked($request->input('invite_to_bar'));
        $reply_to_address = $request->input('reply_to');

        // Get selected locale, reset if invalid
        $default_locale = $request->input('language');
        if(!langManager()->isValidLocale($default_locale))
            $default_locale = null;

        // TODO: ensure we've found any aliases to send updates for, report
        // error otherwise!

        // Dispatch background jobs to send updates
        BalanceImportSystemMailUpdates::dispatch(
            $system->id,
            $limit_last_event ? $last_event->id : null,
            $mail_unregistered_users,
            $mail_not_joined_users,
            $mail_joined_users,
            $message,
            $related_bar_id,
            $invite_to_bar,
            $default_locale,
            $reply_to_address,
        );

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.economy.balanceimport.event.index', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
                'systemId' => $system->id,
            ])
            ->with('success', __('pages.balanceImportMailBalance.sentBalanceUpdateEmail'));
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
