<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Jobs\ProcessBunqAccountEvents;
use App\Models\BunqAccount;
use App\Scopes\EnabledScope;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use bunq\Context\ApiContext;
use bunq\Context\BunqContext;
use bunq\Exception\BadRequestException;
use bunq\Http\Pagination;
use bunq\Model\Generated\Endpoint\EventApiObject;
use bunq\Model\Generated\Endpoint\MonetaryAccountBankApiObject;
use bunq\Util\BunqEnumApiEnvironmentType;

class BunqAccountController extends Controller {

    // TODO: make this controller generic, also support it for application
    //       glboal configuration?

    /**
     * Bunq account index page for communities.
     *
     * @return Response
     */
    public function index(Request $request, $communityId) {
        $community = \Request::get('community');
        $accounts = $community
            ->bunqAccounts()
            ->withoutGlobalScope(new EnabledScope('enable_payments'))
            ->get();

        return view('community.bunqAccount.index')
            ->with('accounts', $accounts);
    }

    /**
     * bunq account creation page.
     *
     * @return Response
     */
    public function create(Request $request, $communityId) {
        return view('community.bunqAccount.create');
    }

    /**
     * Payment service create endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    // TODO: duplicate function, very similar to AppBunqAccountController::doCreate
    public function doCreate(Request $request, $communityId) {
        // Get the community
        $community = \Request::get('community');

        // Validate
        $request->validate([
            'name' => 'required|' . ValidationDefaults::NAME,
            'environment' => 'required|in:production,sandbox',
            'token' => 'required|' . ValidationDefaults::BUNQ_TOKEN,
            'account_holder' => 'required|' . ValidationDefaults::NAME,
            'iban' => 'required|iban|regex:/[A-Z]{2}\d\dBUNQ[0-9]+/|unique:bunq_account,iban',
            'bic' => 'nullable|bic',
            'confirm' => 'accepted',
        ], [
            'iban.regex' => __('pages.bunqAccounts.mustEnterBunqIban'),
            'iban.unique' => __('pages.bunqAccounts.accountAlreadyUsed'),
        ]);

        // Gather fats
        $account_holder = $request->input('account_holder');
        $iban = $request->input('iban');
        // TODO: user cannot enter bic?
        $bic = $request->input('bic') ?? 'BUNQNL2A';

        // Create an API context for this application instance, load the context
        try {
            // Select the environment
            switch($request->input('environment')) {
            case 'production':
                $environment = BunqEnumApiEnvironmentType::PRODUCTION();
                break;
            case 'sandbox':
                $environment = BunqEnumApiEnvironmentType::SANDBOX();
                break;
            default:
                throw new \Exception('Unknown bunq environment selected');
            }

            // Create the API context, obtain a session
            $apiContext = ApiContext::create(
                $environment,
                $request->input('token'),
                config('app.name') . ' ' . config('app.url'),
                []
            );
            BunqContext::loadApiContext($apiContext);
        } catch(BadRequestException $e) {
            add_session_error('token', __('pages.bunqAccounts.invalidApiToken'));
            return redirect()->back()->withInput();
        }

        // Find a monetary account that matches the given IBAN
        $monetaryAccount = Self::findBunqAccountWithIban($iban);
        if($monetaryAccount == null) {
            add_session_error('iban', __('pages.bunqAccounts.noAccountWithIban'));
            return redirect()->back()->withInput();
        }

        // Must use euro and have a zero balance
        $balance = $monetaryAccount->getBalance();
        // TODO: assert list of supported currencies somewhere
        if($balance->getCurrency() != 'EUR') {
            add_session_error('iban', __('pages.bunqAccounts.onlyEuroSupported'));
            return redirect()->back()->withInput();
        }
        if($balance->getValue() != '0.00') {
            add_session_error('iban', __('pages.bunqAccounts.notZeroBalance'));
            return redirect()->back()->withInput();
        }

        // List the last account event, obtain its ID
        $events = EventApiObject::listing([
                'monetary_account_id' => $monetaryAccount->getId(),
                'status' => 'FINALIZED',
                'count' => 1,
            ], [])->getValue();
        $last_event_id = collect($events)
            ->map(function($event) {
                return $event->getId();
            })
            ->first();

        // Add the bunq account to the database
        $account = new BunqAccount();
        $account->community_id = $community->id;
        $account->enable_payments = is_checked($request->input('enable_payments'));
        $account->enable_checks = is_checked($request->input('enable_checks'));
        $account->name = $request->input('name');
        $account->api_context = $apiContext;
        $account->monetary_account_id = $monetaryAccount->getId();
        $account->account_holder = $account_holder;
        $account->iban = $iban;
        $account->bic = $bic;
        $account->last_event_id = $last_event_id;
        $account->save();

        // Update the bunq account settings, configure things like callbacks
        $message = $account->updateBunqAccountSettings();

        // Redirect to services index
        $message = redirect()
            ->route('community.bunqAccount.show', [
                'communityId' => $community->human_id,
                'accountId' => $account->id,
            ])
            ->with('success', __('pages.bunqAccounts.added'));
        if(!empty($message))
            $response = $response->with('warning', $message);
        return $response;
    }

    /**
     * bunq sandbox account creation page.
     *
     * @return Response
     */
    public function createSandbox(Request $request, $communityId) {
        return view('community.bunqAccount.createSandbox');
    }

    /**
     * bunq sandbox account creation endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    // TODO: duplicate function, very similar to doCreate and AppBunqAccountController::doCreateSandbox
    public function doCreateSandbox(Request $request, $communityId) {
        // Get the community
        $community = \Request::get('community');

        // Validate
        $request->validate([
            'name' => 'required|' . ValidationDefaults::NAME,
            'confirm' => 'accepted',
        ]);

        // Create new bunq sandbox API token
        $api_token = AppBunqAccountController::createBunqSandboxApiToken();

        // Create an API context for this application instance, load the context
        $apiContext = ApiContext::create(
            BunqEnumApiEnvironmentType::SANDBOX(),
            $api_token,
            config('app.name') . ' ' . config('app.url'),
            []
        );
        BunqContext::loadApiContext($apiContext);

        // Get first monetary account
        $pagination = new Pagination();
        $pagination->setCount(1);
        $monetaryAccounts = MonetaryAccountBankApiObject::listing(
            [],
            $pagination->getUrlParamsCountOnly()
        )->getValue();
        $monetaryAccount = collect($monetaryAccounts)->first();

        // Get facts
        $iban_pointer = collect($monetaryAccount->getAlias())
            ->filter(function($p) {
                return $p->getType() == 'IBAN';
            })
            ->first();
        $iban = $iban_pointer->getValue();
        $account_holder = $iban_pointer->getName();
        // TODO: user cannot enter bic?
        $bic = $request->input('bic') ?? 'BUNQNL2A';

        // Must use euro and have a zero balance
        $balance = $monetaryAccount->getBalance();
        // TODO: assert list of supported currencies somewhere
        if($balance->getCurrency() != 'EUR') {
            add_session_error('iban', __('pages.bunqAccounts.onlyEuroSupported'));
            return redirect()->back()->withInput();
        }
        if($balance->getValue() != '0.00') {
            add_session_error('iban', __('pages.bunqAccounts.notZeroBalance'));
            return redirect()->back()->withInput();
        }

        // List the last account event, obtain its ID
        $events = EventApiObject::listing([
                'monetary_account_id' => $monetaryAccount->getId(),
                'status' => 'FINALIZED',
                'count' => 1,
            ], [])->getValue();
        $last_event_id = collect($events)
            ->map(function($event) {
                return $event->getId();
            })
            ->first();

        // Add the bunq account to the database
        $account = new BunqAccount();
        $account->community_id = $community->id;
        $account->enable_payments = is_checked($request->input('enable_payments'));
        $account->enable_checks = is_checked($request->input('enable_checks'));
        $account->name = $request->input('name');
        $account->api_context = $apiContext;
        $account->monetary_account_id = $monetaryAccount->getId();
        $account->account_holder = $account_holder;
        $account->iban = $iban;
        $account->bic = $bic;
        $account->last_event_id = $last_event_id;
        $account->save();

        // Update the bunq account settings, configure things like callbacks
        $message = $account->updateBunqAccountSettings();

        // Redirect to services index
        $response = redirect()
            ->route('community.bunqAccount.show', [
                'communityId' => $community->human_id,
                'accountId' => $account->id,
            ])
            ->with('success', __('pages.bunqAccounts.added'));
        if(!empty($message))
            $response = $response->with('warning', $message);
        return $response;
    }

    /**
     * List all active monetary accounts within the current bunq context, and
     * find a monetary account that has the given IBAN as alias.
     *
     * If none was found, `null` is returned.
     *
     * @param string $iban IBAN to look for, must be normalized.
     * @return MonetaryAccountBankApiObject|null The monetary bank account, or null.
     */
    private static function findBunqAccountWithIban(string $iban) {
        // Configure pagination
        $pagination = new Pagination();
        $pagination->setCount(200);

        // List all monetary accounts, filter to active accounts
        $monetaryAccounts = MonetaryAccountBankApiObject::listing(
            [],
            $pagination->getUrlParamsCountOnly()
        )->getValue();
        $monetaryAccounts = collect($monetaryAccounts)
            ->filter(function($a)  {
                return $a->getStatus() === 'ACTIVE';
            });

        // Find an account with this IBAN
        return $monetaryAccounts
            ->filter(function($a) use($iban) {
                // Get the account IBAN
                $a_iban = Self::getBunqMonetaryAccountIban($a);
                return $a_iban != null && $a_iban == $iban;
            })
            ->first();
    }

    /**
     * Get the IBAN for a given monetary bank account.
     * If the account does not have an IBAN configured, null is returned.
     *
     * @param MonetaryAccountBankApiObject $monetaryAccount The monetary account.
     * @return string|null The IBAN for this account, or null.
     */
    private static function getBunqMonetaryAccountIban(MonetaryAccountBankApiObject $monetaryAccount) {
        return collect($monetaryAccount->getAlias())
            ->filter(function($alias) {
                return $alias->getType() === 'IBAN';
            })
            ->map(function($alias) {
                return $alias->getValue();
            })
            ->first();
    }

    /**
     * Show a bunq account.
     *
     * @param int $communityId The community ID.
     * @param int $accountId The bunq account ID.
     *
     * @return Response
     */
    public function show($communityId, $accountId) {
        // Get the community, find the bunq account
        $community = \Request::get('community');
        $account = $community
            ->bunqAccounts()
            ->withoutGlobalScope(new EnabledScope('enable_payments'))
            ->findOrFail($accountId);

        return view('community.bunqAccount.show')
            ->with('account', $account);
    }

    /**
     * Run housekeeping for this bunq account.
     *
     * This will reconfigure the used monetary account on bunqs end, to set
     * callback URLs and such thing. And it will queue all pending events for
     * processing.
     *
     * @param int $accountId The bunq account ID.
     *
     * @return Response
     */
    public function doHousekeep($communityId, $accountId) {
        // Get the community, find the bunq account
        $community = \Request::get('community');
        $account = $community
            ->bunqAccounts()
            ->withoutGlobalScope(new EnabledScope('enable_payments'))
            ->findOrFail($accountId);

        // Load the bunq API context
        $account->loadBunqContext();

        // Update bunq account settings, dispatch job to process pending events
        $message = $account->updateBunqAccountSettings();
        ProcessBunqAccountEvents::dispatch($account);

        // Redirect back to the show page
        $response = redirect()
            ->route('community.bunqAccount.show', [
                'communityId' => $community->human_id,
                'accountId' => $accountId
            ])
            ->with('success', __('pages.bunqAccounts.runHousekeepingSuccess'));
        if(!empty($message))
            $response = $response->with('warning', $message);
        return $response;
    }

    /**
     * Edit a bunq account.
     *
     * @param int $communityId The community ID.
     * @param int $accountId The bunq account ID.
     *
     * @return Response
     */
    public function edit($communityId, $accountId) {
        // Get the community, find the bunq account
        $community = \Request::get('community');
        $account = $community
            ->bunqAccounts()
            ->withoutGlobalScope(new EnabledScope('enable_payments'))
            ->findOrFail($accountId);

        return view('community.bunqAccount.edit')
            ->with('account', $account);
    }

    /**
     * bunq account update endpoint.
     *
     * @param Request $request Request.
     * @param int $communityId The community ID.
     * @param int $accountId The bunq account ID.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $accountId) {
        // TODO: with trashed?

        // Get the community, find the bunq account
        $community = \Request::get('community');
        $account = $community
            ->bunqAccounts()
            ->withoutGlobalScope(new EnabledScope('enable_payments'))
            ->findOrFail($accountId);

        // Validate
        $request->validate([
            'name' => 'required|' . ValidationDefaults::NAME,
            'account_holder' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Edit the account
        $account->name = $request->input('name');
        $account->enable_payments = is_checked($request->input('enable_payments'));
        $account->enable_checks = is_checked($request->input('enable_checks'));
        $account->account_holder = $request->input('account_holder');
        $account->save();

        // Redirect the user to the payment service page
        return redirect()
            ->route('community.bunqAccount.show', [
                'communityId' => $community->human_id,
                'accountId' => $account->id,
            ])
            ->with('success', __('pages.bunqAccounts.changed'));
    }

    // /**
    //  * Page for confirming the deletion of the payment service.
    //  *
    //  * @return Response
    //  */
    // public function delete($communityId, $economyId, $serviceId) {
    //     // Get the user, community, find the payment service
    //     $user = barauth()->getUser();
    //     $community = \Request::get('community');
    //     $economy = $community->economies()->findOrFail($economyId);
    //     $service = $economy
    //         ->paymentServices()
    //         ->withTrashed()
    //         ->findOrFail($serviceId);

    //     // TODO: ensure there are no other constraints that prevent deleting the
    //     // product

    //     return view('community.economy.paymentservice.delete')
    //         ->with('economy', $economy)
    //         ->with('service', $service);
    // }

    // /**
    //  * Delete a payment service.
    //  *
    //  * @return Response
    //  */
    // public function doDelete(Request $request, $communityId, $economyId, $serviceId) {
    //     // Get the user, community, find the payment service
    //     $user = barauth()->getUser();
    //     $community = \Request::get('community');
    //     $economy = $community->economies()->findOrFail($economyId);
    //     $service = $economy
    //         ->paymentServices()
    //         ->withTrashed()
    //         ->findOrFail($serviceId);

    //     // TODO: ensure there are no other constraints that prevent deleting the
    //     // product

    //     // Soft delete
    //     $service->delete();

    //     // Redirect to the payment service index
    //     return redirect()
    //         ->route('community.economy.payservice.index', [
    //             'communityId' => $community->human_id,
    //             'economyId' => $economy->id,
    //         ])
    //         ->with('success', __('pages.paymentService.deleted'));
    // }

    // TODO: set proper perms here!
    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return EconomyController::permsView();
    }

    // TODO: set proper perms here!
    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return EconomyController::permsManage();
    }
}
