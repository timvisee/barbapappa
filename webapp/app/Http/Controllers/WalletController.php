<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Models\Currency;
use App\Models\EconomyCurrency;
use App\Perms\Builder\Config as PermsConfig;
use App\Perms\CommunityRoles;

class WalletController extends Controller {

    /**
     * Wallet index page for a community user.
     * This shows a list of economies wallets may be created in.
     * An user clicks on an economy to go to a specialized page for wallet
     * management.
     *
     * @return Response
     */
    public function index($communityId) {
        // Get the community, find the economies
        $community = \Request::get('community');
        // TODO: only get community economies having at least one bar or user wallet
        $economies = $community->economies();

        // Immediately redirect to specific economy if there's only one
        if($economies->limit(2)->count() == 1) {
            $economy = $economies->firstOrFail();
            return redirect()
                ->route('community.wallet.list', ['communityId' => $communityId, 'economyId' => $economy->id]);
        }

        return view('community.wallet.index')
            ->with('economies', $economies->get());
    }

    /**
     * List the user wallets for the specified economy.
     *
     * @return Response
     */
    public function list($communityId, $economyId) {
        // Get the user, community, find economy and user wallets
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallets = $user->wallets()->where('economy_id', $economyId)->with('currency');

        return view('community.wallet.list')
            ->with('economy', $economy)
            ->with('wallets', $wallets->get());
    }

    /**
     * Show an user wallet.
     *
     * @return Response
     */
    public function show($communityId, $economyId, $walletId) {
        // Get the user, community, find the economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->findOrFail($walletId);

        return view('community.wallet.show')
            ->with('economy', $economy)
            ->with('wallet', $wallet);
    }

    /**
     * Page for creating a new user wallet.
     *
     * @return Response
     */
    public function create($communityId, $economyId) {
        // Get the user, community, find the economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // List the currencies an user can create a wallet for
        $currencies = $economy->currencies()->where('allow_wallet', true)->get();

        // Show an error if an user can't create a wallet
        if($currencies->isEmpty()) {
            return redirect()
                ->route('community.wallet.list', ['communityId' => $communityId, 'economyId' => $economyId])
                ->with('error', __('pages.wallets.cannotCreateNoCurrencies'));
        }

        // Show the create view
        return view('community.wallet.create')
            ->with('economy', $economy)
            ->with('currencies', $currencies);
    }

    /**
     * Create a new user wallet.
     *
     * @return Response
     */
    public function doCreate(Request $request, $communityId, $economyId) {
        // Get the user, community, find the economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
            'currency' => array_merge(['required'], ValidationDefaults::walletEconomyCurrency($economy)),
        ]);

        // Find the selected economy currency, get it's currency ID
        $currencyId = EconomyCurrency::findOrFail($request->input('currency'))->currency_id;

        // Create the wallet
        $wallet = $user->wallets()->create([
            'economy_id' => $economyId,
            'name' => $request->input('name'),
            'currency_id' => $currencyId,
        ]);

        // Redirect to the show view after creation
        return redirect()
            ->route('community.wallet.list', ['communityId' => $communityId, 'economyId' => $economy->id])
            ->with('success', __('pages.wallets.walletCreated'));
    }

    /**
     * Page for editing an user wallet.
     *
     * @return Response
     */
    public function edit($communityId, $economyId, $walletId) {
        // Get the user, community, find economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->findOrFail($walletId);

        // Show the edit view
        return view('community.wallet.edit')
            ->with('economy', $economy)
            ->with('wallet', $wallet);
    }

    /**
     * Edit an user wallet.
     *
     * @return Response
     */
    public function doEdit(Request $request, $communityId, $economyId, $walletId) {
        // Get the user, community, find economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->findOrFail($walletId);

        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
        ]);

        // Update the name
        $wallet->name = $request->input('name');
        $wallet->save();

        // Redirect to the show view after editing
        return redirect()
            ->route('community.wallet.list', ['communityId' => $communityId, 'economyId' => $economyId])
            ->with('success', __('pages.wallets.walletUpdated'));
    }

    /**
     * Page for confirming the deletion of an user wallet.
     *
     * @return Response
     */
    public function delete($communityId, $economyId, $walletId) {
        // Get the user, community, find economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->findOrFail($walletId);

        // Make sure there's exactly zero balance
        if($wallet->balance != 0.00) {
            // Format the zero balance
            $zero = balance(0.00, $wallet->currency->code);

            return redirect()
                ->route('community.wallet.show', ['communityId' => $communityId, 'economyId' => $economyId, 'walletId' => $walletId])
                ->with('error', __('pages.wallets.cannotDeleteNonZeroBalance', ['zero' => $zero]));
        }

        // TODO: ensure there are no other constraints that prevent deleting the
        // wallet

        return view('community.wallet.delete')
            ->with('economy', $economy)
            ->with('wallet', $wallet);
    }

    /**
     * Delete an user wallet.
     *
     * @return Response
     */
    public function doDelete($communityId, $economyId, $walletId) {
        // Get the user, community, find economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $user
            ->wallets()
            ->where('balance', 0.00)
            ->where('economy_id', $economyId)
            ->findOrFail($walletId);

        // TODO: ensure there are no other constraints that prevent deleting the
        // wallet

        // Delete the wallet
        $wallet->delete();

        // Redirect to the list page after deleting
        return redirect()
            ->route('community.wallet.list', ['communityId' => $communityId, 'economyId' => $economy->id])
            ->with('success', __('pages.wallets.walletDeleted'));
    }
}
