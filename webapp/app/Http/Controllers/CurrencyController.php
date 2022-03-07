<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Models\BalanceImportChange;
use App\Perms\Builder\Config as PermsConfig;

class CurrencyController extends Controller {

    /**
     * A list of currency presets.
     */
    public const PRESETS = [
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'format' => '€1.0,00',
        ],
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'format' => '$1.0,00',
        ],
        'GBP' => [
            'name' => 'British Pound',
            'symbol' => '£',
            'format' => '£1.0,00',
        ],
    ];

    /**
     * Currency for community economy index.
     *
     * @return Response
     */
    public function index($communityId, $economyId) {
        // Get the community, find economy, query currencies
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currencies = $economy->currencies()->get();

        return view('community.economy.currency.index')
            ->with('economy', $economy)
            ->with('enabled', $currencies->filter(function($c) { return $c->enabled; }))
            ->with('disabled', $currencies->filter(function($c) { return !$c->enabled; }));
    }

    /**
     * Show the supported currencies for a community economy with the given ID.
     *
     * @return Response
     */
    public function show($communityId, $economyId, $currencyId) {
        // Get the community, find economy and economy currency
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currency = $economy->currencies()->findOrFail($currencyId);

        return view('community.economy.currency.show')
            ->with('economy', $economy)
            ->with('currency', $currency);
    }

    /**
     * Add a new currency for a community economy.
     *
     * @return Response
     */
    public function create($communityId, $economyId) {
        // Get the community, find economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        return view('community.economy.currency.create')
            ->with('economy', $economy);
    }

    /**
     * Create a community economy.
     *
     * @return Response
     */
    public function doCreate(Request $request, $communityId, $economyId) {
        // Get the community, find economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Validate
        $this->validate($request, [
            'name' => ValidationDefaults::NAME,
            'code' => array_merge(['nullable'], ValidationDefaults::currencyCode($economy, true)),
            'symbol' => ValidationDefaults::CURRENCY_SYMBOL,
            'format' => ValidationDefaults::CURRENCY_FORMAT,
        ]);

        // Create the economy currency configuration and save
        $currency = $economy->currencies()->create([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'symbol' => $request->input('symbol'),
            'format' => $request->input('format'),
            'enabled' => is_checked($request->input('enabled')),
            'allow_wallet' => is_checked($request->input('allow_wallet')),
        ]);

        // Redirect to the show view after creation
        return redirect()
            ->route('community.economy.currency.index', ['communityId' => $communityId, 'economyId' => $economy->id])
            ->with('success', __('pages.currencies.currencyCreated'));
    }

    /**
     * Show page to add a currency preset.
     *
     * @return Response
     */
    public function addPreset($communityId, $economyId, $code = null) {
        // Get the community, find economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Grab list of presets, mark already existing currencies
        $existing = $economy->currencies()->pluck('code');
        $presets = collect(Self::PRESETS)
            ->map(function($p, $code) use($existing) {
                $p['exists'] = $code != null && $existing->contains($code);
                return $p;
            });

        // Show currency selection screen
        if($code == null)
            return view('community.economy.currency.add')
                ->with('economy', $economy)
                ->with('presets', $presets);

        // Code must be a known preset
        if(!isset(Self::PRESETS[$code])) {
            add_session_error(null, __('pages.currencies.invalidPreset') . '.');
            return redirect()->back()->withInput();
        }

        return view('community.economy.currency.createPreset')
            ->with('economy', $economy)
            ->with('code', $code)
            ->with('preset', Self::PRESETS[$code]);
    }

    /**
     * Add a community economy preset.
     *
     * @return Response
     */
    public function doAddPreset(Request $request, $communityId, $economyId) {
        // Get the community, find economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Code must be a known preset
        $code = $request->input('code');
        if(!isset(Self::PRESETS[$code])) {
            add_session_error(null, __('pages.currencies.invalidPreset'));
            return redirect()->back()->withInput();
        }

        // Ensure code doesn't exist already in this economy
        $exists = $code != null && $economy->currencies()->where('code', $code)->limit(1)->count() > 0;
        if($exists) {
            add_session_error(null, __('pages.currencies.currencyExists'));
            return redirect()->back()->withInput();
        }

        $preset = Self::PRESETS[$code];

        // Create the economy currency configuration and save
        $currency = $economy->currencies()->create([
            'name' => $preset['name'],
            'code' => $code,
            'symbol' => $preset['symbol'],
            'format' => $preset['format'],
            'enabled' => is_checked($request->input('enabled')),
            'allow_wallet' => is_checked($request->input('allow_wallet')),
        ]);

        // Redirect to the show view after creation
        return redirect()
            ->route('community.economy.currency.index', ['communityId' => $communityId, 'economyId' => $economy->id])
            ->with('success', __('pages.currencies.currencyCreated'));
    }

    /**
     * The edit page for a currency of an economy.
     *
     * @return Response
     */
    public function edit($communityId, $economyId, $currencyId) {
        // Get the community, find economy and economy currency
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currency = $economy->currencies()->findOrFail($currencyId);

        // Show the edit view
        return view('community.economy.currency.edit')
            ->with('economy', $economy)
            ->with('currency', $currency);
    }

    /**
     * Edit a currency of an economy.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $economyId, $currencyId) {
        // Get the community, find economy and economy currency
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currency = $economy->currencies()->findOrFail($currencyId);

        // Validate
        $this->validate($request, [
            'name' => ValidationDefaults::NAME,
            'symbol' => ValidationDefaults::CURRENCY_SYMBOL,
            'format' => ValidationDefaults::CURRENCY_FORMAT,
        ]);

        // Update the properties
        $currency->name = $request->input('name');
        $currency->symbol = $request->input('symbol');
        $currency->format = $request->input('format');
        $currency->enabled = is_checked($request->input('enabled'));
        $currency->allow_wallet = is_checked($request->input('allow_wallet'));
        $currency->save();

        // Redirect to the show view after editing
        return redirect()
            ->route('community.economy.currency.index', ['communityId' => $communityId, 'economyId' => $economyId])
            ->with('success', __('pages.currencies.currencyUpdated'));
    }

    /**
     * The page to delete a currency of an economy.
     *
     * @return Response
     */
    public function delete($communityId, $economyId, $currencyId) {
        // Get the community, and the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currency = $economy->currencies()->findOrFail($currencyId);

        return view('community.economy.currency.delete')
            ->with('economy', $economy)
            ->with('currency', $currency);
    }

    /**
     * Delete a currency of an economy.
     *
     * @return Response
     */
    public function doDelete($communityId, $economyId, $currencyId) {
        // Get the community, find the economy
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $currency = $economy->currencies()->findOrFail($currencyId);

        // Check some requirements
        $hasCurrency = $economy->wallets()->where('currency_id', $currencyId)->limit(1)->count() > 0;
        if($hasCurrency)
            return redirect()
                ->back()
                ->with('error', __('pages.currencies.cannotDeleteHasWallet'));
        $hasMutation = $economy->mutations()->where('currency_id', $currencyId)->limit(1)->count() > 0;
        if($hasMutation)
            return redirect()
                ->back()
                ->with('error', __('pages.currencies.cannotDeleteHasMutation'));
        $hasPayment = $economy->payments()->where('payment.currency_id', $currencyId)->limit(1)->count() > 0;
        if($hasPayment)
            return redirect()
                ->back()
                ->with('error', __('pages.currencies.cannotDeleteHasPayment'));
        $hasService = $economy->paymentServices()->where('currency_id', $currencyId)->limit(1)->count() > 0;
        if($hasService)
            return redirect()
                ->back()
                ->with('error', __('pages.currencies.cannotDeleteHasService'));
        $hasChange = BalanceImportChange::where('currency_id', $currencyId)->limit(1)->count() > 0;
        if($hasChange)
            return redirect()
                ->back()
                ->with('error', __('pages.currencies.cannotDeleteHasChange'));

        // Delete the economy currency configuration
        $currency->delete();

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.economy.currency.index', ['communityId' => $communityId, 'economyId' => $economy->id])
            ->with('success', __('pages.currencies.currencyDeleted'));
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
