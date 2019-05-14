<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Currency;
use App\Models\EconomyCurrency;
use App\Models\Mutation;
use App\Models\MutationWallet;
use App\Models\Transaction;
use App\Perms\Builder\Config as PermsConfig;
use App\Perms\CommunityRoles;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;

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
        $wallets = $economy->userWallets($user)->with('currency')->with('currency');

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
        // TODO: do some permission checking?

        // Get the user, community, find the economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->findOrFail($walletId);
        $transactions = $wallet->lastTransactions();

        return view('community.wallet.show')
            ->with('economy', $economy)
            ->with('wallet', $wallet)
            ->with('transactions', $transactions->get());
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
        // TODO: create wallet through model!
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

    /**
     * Show the wallet transfer page.
     *
     * @return Response
     */
    public function transfer($communityId, $economyId, $walletId) {
        // TODO: do some permission checking?

        // Get the user, community, find the economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->findOrFail($walletId);
        $toWallets = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->where('currency_id', $wallet->currency_id)
            ->where('id', '<>', $walletId)
            ->get();

        return view('community.wallet.transfer')
            ->with('economy', $economy)
            ->with('wallet', $wallet)
            ->with('toWallets', $toWallets)
            ->with('currency', $wallet->currency);
    }

    /**
     * Do the wallet transfer.
     *
     * @return Response
     */
    public function doTransfer(Request $request, $communityId, $economyId, $walletId) {
        // TODO: do some permission checking?

        // Get the user, community, find the economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->findOrFail($walletId);
        $toWallets = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->where('currency_id', $wallet->currency_id)
            ->where('id', '<>', $walletId)
            ->get();
        $currency = $wallet->currency;

        // Validate
        $this->validate($request, [
            'amount' => ['required', ValidationDefaults::PRICE_POSITIVE],
            'to_wallet' => [
                'required',
                Rule::in($toWallets->pluck('id')->push('new')),
            ],
        ]);
        $amount = $request->input('amount');
        $toWallet = $request->input('to_wallet');

        // Start a database transaction for the wallet transaction
        DB::transaction(function() use($user, $economy, $currency, $amount, $wallet, &$toWallet, $toWallets) {
            // Create a new wallet or select an existing wallet
            if($toWallet == 'new')
                $toWallet = $user->createWallet($economy, $currency->id);
            else
                $toWallet = $toWallets->firstWhere('id', $toWallet);

            // Create the transaction
            $transaction = Transaction::create([
                'state' => Transaction::STATE_SUCCESS,
                'owner_id' => $user->id,
            ]);

            // Create the from wallet mutation
            $mut_wallet = $transaction
                ->mutations()
                ->create([
                    'economy_id' => $economy->id,
                    'type' => Mutation::TYPE_WALLET,
                    'amount' => $amount,
                    'currency_id' => $currency->id,
                    'state' => Mutation::STATE_SUCCESS,
                    'owner_id' => $user->id,
                ]);
            MutationWallet::create([
                'mutation_id' => $mut_wallet->id,
                'wallet_id' => $wallet->id,
            ]);

            // Create the to wallet mutation
            $mut_wallet = $transaction
                ->mutations()
                ->create([
                    'economy_id' => $economy->id,
                    'type' => Mutation::TYPE_WALLET,
                    'amount' => -$amount,
                    'currency_id' => $currency->id,
                    'state' => Mutation::STATE_SUCCESS,
                    'owner_id' => $user->id,
                ]);
            MutationWallet::create([
                'mutation_id' => $mut_wallet->id,
                'wallet_id' => $toWallet->id,
            ]);

            // Transfer the money
            $wallet->transfer($amount, $toWallet);
        });

        // Redirect back to the wallet page with a success message
        return redirect()
            ->route('community.wallet.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'walletId' => $toWallet->id,
            ])
            ->with('success', __('pages.wallets.successfullyTransferredAmount', [
                'amount' => $wallet->currency->formatAmount($amount),
                'wallet' => $toWallet->name,
            ]));
    }

    /**
     * Show the wallet transfer page.
     *
     * @return Response
     */
    public function transferUser($communityId, $economyId, $walletId) {
        // TODO: do some permission checking?

        // Get the user, community, find the economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->findOrFail($walletId);

        return view('community.wallet.transferUser')
            ->with('economy', $economy)
            ->with('wallet', $wallet);
    }

    /**
     * Show user wallet transactions.
     *
     * @return Response
     */
    public function transactions($communityId, $economyId, $walletId) {
        // TODO: do some permission checking?

        // Get the user, community, find the economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $user
            ->wallets()
            ->where('economy_id', $economyId)
            ->findOrFail($walletId);
        $transactions = $wallet->transactions();

        return view('community.wallet.transactions')
            ->with('economy', $economy)
            ->with('wallet', $wallet)
            ->with('transactions', $transactions->get());
    }
}
