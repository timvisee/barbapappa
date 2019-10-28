<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Currency;
use App\Models\EconomyCurrency;
use App\Models\EconomyMember;
use App\Models\Mutation;
use App\Models\MutationPayment;
use App\Models\MutationWallet;
use App\Models\Transaction;
use App\Models\Wallet;
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
        $user = barauth()->getUser();
        $community = \Request::get('community');
        // TODO: only get community economies having at least one bar or user wallet
        $economies = $community->economies()->get();

        // Add user wallet count to economy objects
        $economies = $economies->map(function($economy) use($user) {
            $member = $economy->members()->user($user)->first();
            $economy->user_wallet_count = $member != null ? $member->wallets()->count() : 0;
            return $economy;
        });

        return view('community.wallet.index')
            ->with('economies', $economies);
    }

    /**
     * List the user wallets for the specified economy.
     *
     * @return Response
     */
    public function list($communityId, $economyId) {
        // Get the user, community, find economy, economy member and user wallets
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $economy_member = $economy->members()->user($user)->first();
        $wallets = $economy_member != null
            ? $economy_member->wallets()->get()
            : [];

        return view('community.wallet.list')
            ->with('economy', $economy)
            ->with('wallets', $wallets);
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
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $wallet = $economy_member
            ->wallets()
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
        // Get the community, find the economy and wallet
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
        // Get the user, community, find the economy
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Ensure user is economy member, get the member
        if(!$economy->isJoined($user))
            $economy->join($user);
        $economy_member = $economy
            ->members()
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Validate
        $this->validate($request, [
            'name' => 'required|' . ValidationDefaults::NAME,
            'currency' => array_merge(['required'], ValidationDefaults::walletEconomyCurrency($economy)),
        ]);

        // Find the selected economy currency, get it's currency ID
        $currencyId = EconomyCurrency::findOrFail($request->input('currency'))->currency_id;

        // Create the wallet
        $wallet = $economy_member->wallets()->create([
            'economy_id' => $economy->id,
            'user_id' => $user->id,
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
        // Get the user, community, find economy, economy member and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $wallet = $economy_member->wallets()->findOrFail($walletId);

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
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $wallet = $economy_member->wallets()->findOrFail($walletId);

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
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $wallet = $economy_member->wallets()->findOrFail($walletId);

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
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $wallet = $economy_member
            ->wallets()
            ->where('balance', 0.00)
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
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $wallet = $economy_member->wallets()->findOrFail($walletId);
        $toWallets = $economy_member
            ->wallets()
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
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $wallet = $economy_member->wallets()->findOrFail($walletId);
        $toWallets = $economy_member
            ->wallets()
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
        $amount = normalize_price($request->input('amount'));
        $toWallet = $request->input('to_wallet');
        $newWallet = $toWallet == 'new';

        // Start a database transaction for the wallet transaction
        DB::transaction(function() use($user, $economy, $economy_member, $currency, $amount, $wallet, $newWallet, &$toWallet, $toWallets) {
            // Create a new wallet or select an existing wallet
            if($newWallet)
                $toWallet = $economy_member->createWallet($economy, $currency->id);
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
                    'mutationable_id' => 0,
                    'mutationable_type' => '',
                    'amount' => $amount,
                    'currency_id' => $currency->id,
                    'state' => Mutation::STATE_SUCCESS,
                    'owner_id' => $user->id,
                ]);
            $mut_wallet->setMutationable(
                MutationWallet::create([
                    'wallet_id' => $wallet->id,
                ])
            );

            // Create the to wallet mutation
            $mut_wallet = $transaction
                ->mutations()
                ->create([
                    'economy_id' => $economy->id,
                    'mutationable_id' => 0,
                    'mutationable_type' => '',
                    'amount' => -$amount,
                    'currency_id' => $currency->id,
                    'state' => Mutation::STATE_SUCCESS,
                    'owner_id' => $user->id,
                    'depend_on' => $mut_wallet->id,
                ]);
            $mut_wallet->setMutationable(
                MutationWallet::create([
                    'wallet_id' => $toWallet->id,
                ])
            );

            // Transfer the money
            $wallet->transfer($amount, $toWallet);
        });

        // Redirect back to the wallet page with a success message
        return redirect()
            ->route('community.wallet.' . ($newWallet ? 'edit' : 'show'), [
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
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $wallet = $economy_member->wallets()->findOrFail($walletId);

        return view('community.wallet.transferUser')
            ->with('economy', $economy)
            ->with('wallet', $wallet);
    }

    /**
     * Show the wallet top-up page.
     *
     * @return Response
     */
    public function topUp($communityId, $economyId, $walletId) {
        // TODO: do some permission checking?

        // Get the user, community, find the economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $wallet = $economy_member->wallets()->findOrFail($walletId);
        $services = $economy->paymentServices()->supportsDeposit()->get();

        // TODO: return error if there are no usable services, user can't top-up

        return view('community.wallet.topUp')
            ->with('economy', $economy)
            ->with('wallet', $wallet)
            ->with('currency', $wallet->currency)
            ->with('services', $services);
    }

    /**
     * Do the wallet top-up.
     *
     * @return Response
     */
    public function doTopUp(Request $request, $communityId, $economyId, $walletId) {
        // TODO: do some permission checking?

        // Get the user, community, find the economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $wallet = $economy_member->wallets()->findOrFail($walletId);
        $services = $economy->paymentServices()->supportsDeposit()->get();
        $currency = $wallet->currency;

        // Validate
        $this->validate($request, [
            'amount' => ['required', ValidationDefaults::PRICE_POSITIVE],
            'payment_service' => [
                'required',
                Rule::in($services->pluck('id')),
            ],
        ]);
        $amount = normalize_price($request->input('amount'));
        $service = $services->firstWhere('id', $request->input('payment_service'));

        // Start a database transaction for the top-up
        $payment = null;
        DB::transaction(function() use($user, $economy, $wallet, $service, $currency, $amount, &$payment) {
            // Start a new payment
            $payment = $service->startPayment($currency, $amount, $user);

            // Create the transaction
            $transaction = Transaction::create([
                'state' => Transaction::STATE_PENDING,
                'owner_id' => $user->id,
            ]);

            // Create the payment mutation
            $mut_payment = $transaction
                ->mutations()
                ->create([
                    'economy_id' => $economy->id,
                    'mutationable_id' => 0,
                    'mutationable_type' => '',
                    'amount' => $amount,
                    'currency_id' => $currency->id,
                    'state' => Mutation::STATE_PENDING,
                    'owner_id' => $user->id,
                ]);
            $mut_payment->setMutationable(
                MutationPayment::create([
                    'payment_id' => $payment->id,
                ])
            );

            // Create the to wallet mutation
            $mut_wallet = $transaction
                ->mutations()
                ->create([
                    'economy_id' => $economy->id,
                    'mutationable_id' => 0,
                    'mutationable_type' => '',
                    'amount' => -$amount,
                    'currency_id' => $currency->id,
                    'state' => Mutation::STATE_PENDING,
                    'owner_id' => $user->id,
                    'depend_on' => $mut_payment->id,
                ]);
            $mut_wallet->setMutationable(
                MutationWallet::create([
                    'wallet_id' => $wallet->id,
                ])
            );
        });

        // Redirect to the payment page
        return redirect()->route('payment.pay', [
            'paymentId' => $payment->id,
        ]);
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
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $wallet = $economy_member->wallets()->findOrFail($walletId);
        $transactions = $wallet->transactions();

        return view('community.wallet.transactions')
            ->with('economy', $economy)
            ->with('wallet', $wallet)
            ->with('transactions', $transactions->get());
    }
}
