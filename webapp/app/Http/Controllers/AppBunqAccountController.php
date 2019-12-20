<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\BunqAccount;
use App\Jobs\ProcessBunqAccountEvents;
use BarPay\Models\Service as PayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\Rule;
use Validator;
use bunq\Context\ApiContext;
use bunq\Context\BunqContext;
use bunq\Exception\ApiException;
use bunq\Exception\BadRequestException;
use bunq\Http\Pagination;
use bunq\Model\Generated\Endpoint\Event;
use bunq\Model\Generated\Endpoint\MonetaryAccountBank;
use bunq\Model\Generated\Object\Pointer;
use bunq\Util\BunqEnumApiEnvironmentType;

class AppBunqAccountController extends Controller {

    /**
     * Bunq account index page for communities.
     *
     * @return Response
     */
    public function index(Request $request) {
        $user = barauth()->getUser();
        // TODO: also list disabled accounts
        $accounts = BunqAccount::where('community_id', null)->get();

        return view('app.bunqAccount.index')
            ->with('accounts', $accounts);
    }

    /**
     * bunq account creation page.
     *
     * @return Response
     */
    public function create(Request $request) {
        return view('app.bunqAccount.create');
    }

    /**
     * Payment service create endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doCreate(Request $request) {
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
        $bic = $request->input('bic');

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
        \Debugbar::info($balance);
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
        $events = Event::listing([
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
        $account->community_id = null;
        $account->enabled = is_checked($request->input('enabled'));
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
            ->route('app.bunqAccount.show', [
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
     * @return MonetaryAccountBank|null The monetary bank account, or null.
     */
    private static function findBunqAccountWithIban(string $iban) {
        // Configure pagination
        $pagination = new Pagination();
        $pagination->setCount(200);

        // List all monetary accounts, filter to active accounts
        $monetaryAccounts = MonetaryAccountBank::listing(
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
     * @param MonetaryAccountBank $monetaryAccount The monetary account.
     * @return string|null The IBAN for this account, or null.
     */
    private static function getBunqMonetaryAccountIban(MonetaryAccountBank $monetaryAccount) {
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
     * @param int $accountId The bunq account ID.
     *
     * @return Response
     */
    public function show($accountId) {
        // Find the bunq account
        $account = BunqAccount::where('community_id', null)
            ->findOrFail($accountId);

        return view('app.bunqAccount.show')
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
    public function doHousekeep($accountId) {
        // Find the bunq account
        $account = BunqAccount::where('community_id', null)
            ->findOrFail($accountId);

        // Load the bunq API context
        $account->loadBunqContext();

        // Update bunq account settings, dispatch job to process pending events
        $message = $account->updateBunqAccountSettings();
        ProcessBunqAccountEvents::dispatch($account);

        // Redirect back to the show page
        $response = redirect()
            ->route('app.bunqAccount.show', ['accountId' => $accountId])
            ->with('success', __('pages.bunqAccounts.runHousekeepingSuccess'));
        if(!empty($message))
            $response = $response->with('warning', $message);
        return $response;
    }

    /**
     * Edit a bunq account.
     *
     * @param int $accountId The bunq account ID.
     *
     * @return Response
     */
    public function edit($accountId) {
        // Find the bunq account
        $account = BunqAccount::where('community_id', null)
            ->findOrFail($accountId);

        return view('app.bunqAccount.edit')
            ->with('account', $account);
    }

    /**
     * bunq account update endpoint.
     *
     * @param Request $request Request.
     * @param int $accountId The bunq account ID.
     *
     * @return Response
     */
    public function doEdit(Request $request, $accountId) {
        // TODO: with trashed?

        // Find the bunq account
        $account = BunqAccount::where('community_id', null)->findOrFail($accountId);

        // Validate
        $request->validate([
            'name' => 'required|' . ValidationDefaults::NAME,
            'account_holder' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Edit the account
        $account->name = $request->input('name');
        $account->enabled = is_checked($request->input('enabled'));
        $account->account_holder = $request->input('account_holder');
        $account->save();

        // Redirect the user to the payment service page
        return redirect()
            ->route('app.bunqAccount.show', [
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

    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return AppController::permsAdminister();
    }

    /**
     * The permission required for managing such as editing and deleting.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return AppController::permsAdminister();
    }
}
