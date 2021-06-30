<?php

namespace App\Http\Controllers;

use App\Jobs\CommitBalanceUpdatesForAliases;
use App\Models\BalanceImportAlias;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Validator;

use App\Helpers\ValidationDefaults;

class BalanceImportChangeController extends Controller {

    /**
     * Balance import change index page.
     * This shows a list of balance import changes, each for a different user, grouped
     * to a specific balance import event.
     *
     * @return Response
     */
    public function index(Request $request, $communityId, $economyId, $systemId, $eventId) {
        // Get the community, economy, system and events
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);
        $unapprovedChanges = $event->changes()->approved(false)->get();
        $approvedChanges = $event->changes()->approved(true)->get();

        return view('community.economy.balanceimport.change.index')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('event', $event)
            ->with('unapprovedChanges', $unapprovedChanges)
            ->with('approvedChanges', $approvedChanges);
    }

    /**
     * Balance import change creation page.
     *
     * @return Response
     */
    public function create(Request $request, $communityId, $economyId, $systemId, $eventId) {
        // Get the community and find the economy, system and event
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);
        $currencies = $economy->currencies;

        return view('community.economy.balanceimport.change.create')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('event', $event)
            ->with('currencies', $currencies);
    }

    /**
     * Balance import change create endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doCreate(Request $request, $communityId, $economyId, $systemId, $eventId) {
        // Get the user, community and find the economy, system and event
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);

        // Validate
        $this->validate($request, [
            'currency' => array_merge(['required'], ValidationDefaults::currency($economy)),
            'name' => 'nullable|' . ValidationDefaults::NAME,
            'email' => 'required|' . ValidationDefaults::EMAIL,
            'balance' => ['nullable', 'required_without:cost', ValidationDefaults::PRICE_SIGNED],
            'cost' => ['nullable', 'required_without:balance', ValidationDefaults::PRICE_NOT_ZERO],
        ]);
        $cost = $request->input('cost');
        $balance = $request->input('balance');

        // Only enter final balance or cost, not both
        if(($cost == null) == ($balance == null)) {
            add_session_error('cost', __('pages.balanceImportChange.enterBalanceOrCost'));
            return redirect()->back()->withInput();
        }

        // Normalize the cost and balance
        $cost = $cost != null ? normalize_price($cost) : null;
        $balance = $balance != null ? normalize_price($balance) : null;

        // Obtain the user alias
        $alias = BalanceImportAlias::getOrCreate(
            $economy,
            $request->input('name'),
            $request->input('email')
        );

        // Make sure alias is created
        if($alias == null && empty($request->input('name'))) {
            add_session_error('name', __('pages.balanceImportAlias.newAliasMustProvideName'));
            return redirect()->back()->withInput();
        }

        // Create the balance import change
        $event->changes()->create([
            'alias_id' => $alias->id,
            'balance' => $balance,
            'cost' => $cost,
            'currency_id' => $request->input('currency'),
            'submitter_id' => $user->id,
        ]);

        // Redirect to the show view after creation
        return redirect()
            ->route('community.economy.balanceimport.change.index', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
                'systemId' => $system->id,
                'eventId' => $event->id,
            ])
            ->with('success', __('pages.balanceImportChange.created'));
    }

    /**
     * Balance import change import JSON page.
     *
     * @return Response
     */
    public function importJson(Request $request, $communityId, $economyId, $systemId, $eventId) {
        // Get the community and find the economy, system and event
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);
        $currencies = $economy->currencies;

        return view('community.economy.balanceimport.change.importJson')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('event', $event)
            ->with('currencies', $currencies);
    }

    /**
     * Balance import change import JSON endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doImportJson(Request $request, $communityId, $economyId, $systemId, $eventId) {
        // Get the user, community and find the economy, system and event
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);
        $currencies = $economy->currencies;

        // Validate root fields, parse the JSON data
        $this->validate($request, [
            'currency' => array_merge(['required'], ValidationDefaults::currency($economy)),
            'data' => 'required|json',
        ]);
        $currency_id = (int) $request->input('currency');
        $allow_duplicate = is_checked($request->input('allow_duplicate'));
        $data = json_decode($request->input('data'), true);

        // Parse, validate and normalize each data object
        $economy_symbol = $economy->currencies()->findOrFail($currency_id)->symbol;
        foreach($data as &$item) {
            // Obtain the fields, normalize data
            if($item['name'] != null)
                $item['name'] = trim($item['name']);
            if(empty($item['name']))
                $item['name'] = null;
            if($item['email'] != null)
                $item['email'] = trim($item['email']);
            if($item['balance_new'] != null)
                $item['balance_new'] = trim(str_replace(' ', '', str_replace($economy_symbol, '', $item['balance_new'])));
        }

        // Validate raw fields
        $validator = Validator::make($data, [
            '*.name' => 'nullable|' . ValidationDefaults::NAME,
            '*.email' => 'required|' . ValidationDefaults::EMAIL,
            '*.balance_new' => ['nullable', ValidationDefaults::PRICE_SIGNED],
        ]);

        // Report errors on JSON data fields
        if($validator->fails()) {
            $messages = $validator->messages()->messages();
            foreach($messages as $key => $msgs) {
                $i = explode('.', $key, 2)[0];
                $field = explode('.', $key, 2)[1];
                $name = $key;
                if(isset($data[$i]['email']))
                    $name = '\'' . $data[$i]['email'] . '\'.' . $field;
                foreach($msgs as $msg)
                    add_session_error('data', $name . ': ' . $msg);
            }
            return redirect()->back()->withInput();
        }

        // Create user aliases
        $alias_ids = [];
        foreach($data as &$item) {
            $name = $item['name'];
            // TODO: duplicate, use central function to normalize email addresses
            $email = strtolower(trim($item['email']));

            // Obtain the user alias, make sure it is created
            $alias = BalanceImportAlias::getOrCreate(
                $economy,
                $name,
                $email
            );
            if($alias == null && empty($name)) {
                add_session_error('data', __('pages.balanceImportAlias.newJsonAliasMustProvideName', [
                        'email' => $email,
                    ]));
                return redirect()->back()->withInput();
            }

            // Make sure alias ID is unique
            if(in_array($alias->id, $alias_ids)) {
                add_session_error('data', __('pages.balanceImportAlias.jsonHasDuplicateAlias', [
                        'email' => $email,
                    ]));
                return redirect()->back()->withInput();
            }
            $alias_ids[] = $alias->id;

            // Should not have duplicate aliases in event
            if(!$allow_duplicate) {
                $has_duplicate = $event
                    ->changes()
                    ->where('alias_id', $alias->id)
                    ->whereNotNull('balance')
                    ->limit(1)
                    ->count() > 0;
                if($has_duplicate) {
                    add_session_error('data', __('pages.balanceImportAlias.aliasAlreadyInEvent', [
                            'email' => $email,
                        ]));
                    return redirect()->back()->withInput();
                }
            }
            $alias_ids[] = $alias->id;

            $item['alias_id'] = $alias->id;
        }

        // Create the balance import changes
        DB::transaction(function() use($data, $event, $currency_id, $user) {
            foreach($data as $item) {
                $event->changes()->create([
                    'alias_id' => $item['alias_id'],
                    'balance' => normalize_price($item['balance_new']),
                    'currency_id' => $currency_id,
                    'submitter_id' => $user->id,
                ]);
            }
        });

        // Redirect to the show view after creation
        return redirect()
            ->route('community.economy.balanceimport.change.index', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
                'systemId' => $system->id,
                'eventId' => $event->id,
            ])
            ->with('success', __('pages.balanceImportChange.importedJson'));
    }

    /**
     * Show a balance import change.
     *
     * @return Response
     */
    public function show($communityId, $economyId, $systemId, $eventId, $changeId) {
        // Get the community, economy, find the system, event and change
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);
        $change = $event->changes()->findOrFail($changeId);

        return view('community.economy.balanceimport.change.show')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('event', $event)
            ->with('change', $change);
    }

    /**
     * Page for approving balance import change.
     *
     * @return Response
     */
    public function approve($communityId, $economyId, $systemId, $eventId, $changeId) {
        // Get the community, economy, find the system, event and change
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);
        $change = $event->changes()->findOrFail($changeId);

        // Must not be approved yet
        if($change->isApproved())
            return redirect()->back();

        // Require previous change to be approved, and following not to be
        if($change->balance != null) {
            $previous = $change->previous()->first();
            if($previous != null && !$previous->isApproved())
                return redirect()
                    ->back()
                    ->with('error', __('pages.balanceImportChange.mustApprovePreviousFirst'));
            $following_approved = $change->following()->approved()->limit(1)->count() > 0;
            if($following_approved)
                return redirect()
                    ->back()
                    ->with('error', __('pages.balanceImportChange.cannotApproveWithFollowingApproved'));
        }

        return view('community.economy.balanceimport.change.approve')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('event', $event)
            ->with('change', $change);
    }

    /**
     * Approve the balance import change.
     *
     * @return Response
     */
    public function doApprove(Request $request, $communityId, $economyId, $systemId, $eventId, $changeId) {
        // Get the community, economy, find the system, event and change
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);
        $change = $event->changes()->findOrFail($changeId);

        // Must not be approved yet
        if($change->isApproved())
            return redirect()->back();

        // Require previous change to be approved, and following not to be
        if($change->balance != null) {
            $previous = $change->previous()->first();
            if($previous != null && !$previous->isApproved())
                return redirect()
                    ->back()
                    ->with('error', __('pages.balanceImportChange.mustApprovePreviousFirst'));
            $following_approved = $change->following()->approved()->limit(1)->count() > 0;
            if($following_approved)
                return redirect()
                    ->back()
                    ->with('error', __('pages.balanceImportChange.cannotApproveWithFollowingApproved'));
        }

        // Approve the change
        $change->approve();

        // Redirect to the index page after approving
        return redirect()
            ->route('community.economy.balanceimport.change.index', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
                'systemId' => $system->id,
                'eventId' => $event->id,
            ])
            ->with('success', __('pages.balanceImportChange.approved'));
    }

    /**
     * Page for approving all balance import changes.
     *
     * @return Response
     */
    public function approveAll($communityId, $economyId, $systemId, $eventId) {
        // Get the community, economy, find the system, event and changes
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);
        $changes = $event->changes()->approved(false)->get();

        // Require all previous changes to be approved, and following not to be
        foreach($changes as $change) {
            if($change->balance != null) {
                $previous = $change->previous()->first();
                if($previous != null && !$previous->isApproved())
                    return redirect()
                        ->back()
                        ->with('error', __('pages.balanceImportChange.mustApproveAllPreviousFirst'));
                $following_approved = $change->following()->approved()->limit(1)->count() > 0;
                if($following_approved)
                    return redirect()
                        ->back()
                        ->with('error', __('pages.balanceImportChange.cannotApproveWithFollowingApproved'));
            }
        }

        return view('community.economy.balanceimport.change.approveAll')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('event', $event)
            ->with('changes', $changes);
    }

    /**
     * Approve the balance import change.
     *
     * @return Response
     */
    public function doApproveAll(Request $request, $communityId, $economyId, $systemId, $eventId) {
        // Get the community, economy, find the system, event and changes
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);
        $changes = $event->changes()->approved(false)->get();

        // Require all previous changes to be approved, and following not to be
        foreach($changes as $change) {
            if($change->balance != null) {
                $previous = $change->previous()->first();
                if($previous != null && !$previous->isApproved())
                    return redirect()
                        ->back()
                        ->with('error', __('pages.balanceImportChange.mustApproveAllPreviousFirst'));
                $following_approved = $change->following()->approved()->limit(1)->count() > 0;
                if($following_approved)
                    return redirect()
                        ->back()
                        ->with('error', __('pages.balanceImportChange.cannotApproveWithFollowingApproved'));
            }
        }

        // Approve all changes
        DB::transaction(function() use($changes) {
            foreach($changes as $change)
                $change->approve(false);

            // Commit changes in the background
            CommitBalanceUpdatesForAliases::dispatch(
                $changes->pluck('alias_id')->unique()
            );
        });

        // Redirect to the index page after approving
        return redirect()
            ->route('community.economy.balanceimport.change.index', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
                'systemId' => $system->id,
                'eventId' => $event->id,
            ])
            ->with('success', __('pages.balanceImportChange.approvedAll'));
    }

    /**
     * Page for undoing balance import change.
     *
     * @return Response
     */
    public function undo($communityId, $economyId, $systemId, $eventId, $changeId) {
        // Get the community, economy, find the system, event and change
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);
        $change = $event->changes()->findOrFail($changeId);

        // Must be approved
        if(!$change->isApproved())
            return redirect()->back();

        // Require no newer approved changes to exist
        if($change->balance != null) {
            $followingApproved = $change->following()->whereNotNull('approved_at')->limit(1)->count() > 0;
            if($followingApproved)
                return redirect()
                    ->back()
                    ->with('error', __('pages.balanceImportChange.cannotUndoIfNewerApproved'));
        }

        return view('community.economy.balanceimport.change.undo')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('event', $event)
            ->with('change', $change);
    }

    /**
     * Undo the balance import change.
     *
     * @return Response
     */
    public function doUndo(Request $request, $communityId, $economyId, $systemId, $eventId, $changeId) {
        // Get the community, economy, find the system and event
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);
        $change = $event->changes()->findOrFail($changeId);

        // Must be approved
        if(!$change->isApproved())
            return redirect()->back();

        // Require no newer approved changes to exist
        if($change->balance != null) {
            $followingApproved = $change->following()->whereNotNull('approved_at')->limit(1)->count() > 0;
            if($followingApproved)
                return redirect()
                    ->back()
                    ->with('error', __('pages.balanceImportChange.cannotUndoIfNewerApproved'));
        }

        // Undo the change
        $change->undo();

        // Redirect to the index page after undoing
        return redirect()
            ->route('community.economy.balanceimport.change.show', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
                'systemId' => $system->id,
                'eventId' => $event->id,
                'changeId' => $change->id,
            ])
            ->with('success', __('pages.balanceImportChange.undone'));
    }

    /**
     * Page for confirming the deletion of balance import change.
     *
     * @return Response
     */
    public function delete($communityId, $economyId, $systemId, $eventId, $changeId) {
        // Get the community, economy, find the system, event and change
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);
        $change = $event->changes()->findOrFail($changeId);

        // Require it not to be approved
        if($change->isApproved())
            return redirect()
                ->back()
                ->with('error', __('pages.balanceImportChange.cannotDeleteMustUndo'));

        return view('community.economy.balanceimport.change.delete')
            ->with('economy', $economy)
            ->with('system', $system)
            ->with('event', $event)
            ->with('change', $change);
    }

    /**
     * Delete the balance import change.
     *
     * @return Response
     */
    public function doDelete(Request $request, $communityId, $economyId, $systemId, $eventId, $changeId) {
        // Get the community, economy, find the system and event
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $system = $economy->balanceImportSystems()->findOrFail($systemId);
        $event = $system->events()->findOrFail($eventId);
        $change = $event->changes()->findOrFail($changeId);

        // Require it not to be approved
        if($change->isApproved())
            return redirect()
                ->back()
                ->with('error', __('pages.balanceImportChange.cannotDeleteMustUndo'));

        // Delete the change
        $change->delete();

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.economy.balanceimport.change.index', [
                'communityId' => $communityId,
                'economyId' => $economy->id,
                'systemId' => $system->id,
                'eventId' => $event->id,
            ])
            ->with('success', __('pages.balanceImportChange.deleted'));
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
