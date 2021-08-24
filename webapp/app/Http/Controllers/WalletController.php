<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Currency;
use App\Models\Mutation;
use App\Models\MutationMagic;
use App\Models\MutationPayment;
use App\Models\MutationProduct;
use App\Models\MutationWallet;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Perms\CommunityRoles;
use App\Utils\MoneyAmount;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WalletController extends Controller {

    /**
     * The maximum wallet balance history period.
     */
    const WALLET_BALANCE_HISTORY_PERIOD = '1 month';

    const PAGINATE_ITEMS = 50;

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
            ? $economy_member->wallets()->paginate(10)
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
        // Get the user, community, find the economy and wallet
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $economy->wallets()->findOrFail($walletId);

        // User must have permission
        if(!$wallet->hasViewPermission())
            return response(view('noPermission'));

        $transactions = $wallet->lastTransactions()->get();

        // Get balance graph data
        $balance_graph_data = self::chartBalanceGraph($wallet);

        return view('community.wallet.show')
            ->with('economy', $economy)
            ->with('wallet', $wallet)
            ->with('transactions', $transactions)
            ->with('balance_graph_data', $balance_graph_data);
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
        $currencies = $economy->currencies()->enabled()->where('allow_wallet', true)->get();

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
            'currency' => array_merge(['required'], ValidationDefaults::walletCurrency($economy)),
        ]);

        // Create the wallet
        $wallet = $economy_member->wallets()->create([
            'economy_id' => $economy->id,
            'user_id' => $user->id,
            'name' => $request->input('name'),
            'currency_id' => (int) $request->input('currency'),
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
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $economy->wallets()->findOrFail($walletId);

        // User must have permission
        if(!$wallet->hasManagePermission())
            return response(view('noPermission'));

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
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $economy->wallets()->findOrFail($walletId);

        // User must have permission
        if(!$wallet->hasManagePermission())
            return response(view('noPermission'));

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
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $economy->wallets()->findOrFail($walletId);

        // User must have permission
        if(!$wallet->hasManagePermission())
            return response(view('noPermission'));

        // Make sure there's exactly zero balance
        if($wallet->balance != 0.00) {
            // Format the zero balance
            $zero = $wallet->currency->format(0.0);

            return redirect()
                ->route('community.wallet.show', ['communityId' => $communityId, 'economyId' => $economyId, 'walletId' => $walletId])
                ->with('error', __('pages.wallets.cannotDeleteNonZeroBalance', ['zero' => $zero]));
        }

        // TODO: ensure there are no other constraints that prevent deleting the wallet

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
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $economy
            ->wallets()
            ->where('balance', 0.00)
            ->findOrFail($walletId);

        // User must have permission
        if(!$wallet->hasManagePermission())
            return response(view('noPermission'));

        // TODO: ensure there are no other constraints that prevent deleting the wallet

        // Delete the wallet
        $wallet->delete();

        // Redirect to the list page after deleting
        return redirect()
            ->route('community.wallet.list', ['communityId' => $communityId, 'economyId' => $economy->id])
            ->with('success', __('pages.wallets.walletDeleted'));
    }

    /**
     * Wallet merge page.
     *
     * @return Response
     */
    public function merge($communityId, $economyId) {
        // Get the user, community, find economy, economy member and user wallets
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $currencyWallets = $economy_member
            ->wallets()
            ->get()
            ->groupBy('currency_id')
            ->filter(function($group) {
                return $group->count() > 1;
            });

        // Must have a group to merge wallets in
        if($currencyWallets->isEmpty())
            return redirect()
                ->route('community.wallet.list', [
                    'communityId' => $communityId,
                    'economyId' => $economyId,
                ])
                ->with('info', __('pages.wallets.noWalletsToMerge'));

        // Create array with group objects having currency and wallets
        $currencies = Currency::whereIn('id', $currencyWallets->keys())->get();
        $walletGroups = [];
        foreach($currencyWallets as $currency_id => $wallets) {
            $walletGroups[] = [
                'currency_id' => $currency_id,
                'currency' => $currencies->firstWhere('id', $currency_id),
                'wallets' => $wallets,
            ];
        }

        return view('community.wallet.merge')
            ->with('economy', $economy)
            ->with('walletGroups', $walletGroups);
    }

    /**
     * Do merge wallets page.
     *
     * @return Response
     */
    public function doMerge(Request $request, $communityId, $economyId) {
        // Get the user, community, find economy, economy member and user wallets
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $currencyWallets = $economy_member
            ->wallets()
            ->get()
            ->groupBy('currency_id')
            ->filter(function($group) {
                return $group->count() > 1;
            });

        // Must have a group to merge wallets in
        if($currencyWallets->isEmpty())
            return redirect()
                ->route('community.wallet.list', [
                    'communityId' => $communityId,
                    'economyId' => $economyId,
                ])
                ->with('info', __('pages.wallets.noWalletsToMerge'));

        // Validate and group the wallets to merge
        $mergeWallets = [];
        foreach($currencyWallets as $currency_id => $wallets) {
            $toMerge = collect();
            foreach($wallets as $wallet)
                if(is_checked($request->input($currency_id . '_' . $wallet->id . '_merge')))
                    $toMerge->push($wallet);

            // Continue if none selected, error if 1 selected
            if(empty($toMerge))
                continue;
            if(count($toMerge) == 1) {
                // Get the currency
                $currency = Currency::find($currency_id);
                add_session_error(
                    $currency_id . '_group',
                    __('pages.wallets.mustSelectTwoToMerge', [
                        'currency' => $currency->name,
                    ])
                );
                return redirect()
                    ->back()
                    ->withInput();
            }

            // Add to list to merge
            $mergeWallets[] = $toMerge;
        }

        // Merge selected grouped wallets
        $mergeTotal = 0;
        DB::transaction(function() use(&$mergeTotal, $mergeWallets) {
            foreach($mergeWallets as $wallets) {
                // Merge the selected wallets
                $newWallet = $wallets->first();
                foreach($wallets->slice(1) as $oldWallet) {
                    $oldWallet->migrateTransactions($newWallet);
                    if($oldWallet->balance != 0)
                        throw new \Exception('failed to merge wallets, balance != 0');
                    $oldWallet->delete();
                }

                $mergeTotal += $wallets->count();
            }
        });

        return redirect()
            ->route('community.wallet.list', [
                'communityId' => $communityId,
                'economyId' => $economyId,
            ])
            ->with('success', trans_choice('pages.wallets.mergedWallets#', $mergeTotal) . '.');
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

        // // User must have permission
        // if(!$wallet->hasManagePermission())
        //     return response(view('noPermission'));

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
                $toWallet = $economy_member->createWallet($currency->id);
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
                'amount' => $wallet->currency->format($amount),
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
        $currency = $wallet->currency;
        $services = $economy
            ->paymentServices()
            ->enabled()
            ->supportsCurrency($currency)
            ->supportsDeposit()
            ->get();

        // There must be a usable service
        if($services->isEmpty())
            return redirect()
                ->route('community.wallet.show', [
                    'communityId' => $communityId,
                    'economyId' => $economyId,
                    'walletId' => $walletId,
                ])
                ->with('error', __('pages.wallets.noServiceConfiguredCannotTopUp', ['app' => config('app.name')]));

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
        $currency = $wallet->currency;
        $services = $economy
            ->paymentServices()
            ->enabled()
            ->supportsCurrency($currency)
            ->supportsDeposit()
            ->get();

        // Validate
        $this->validate($request, [
            'amount' => ['nullable', 'required_without:amount_custom', ValidationDefaults::PRICE_POSITIVE],
            'amount_custom' => ['nullable', ValidationDefaults::PRICE_POSITIVE],
            'payment_service' => [
                'required',
                Rule::in($services->pluck('id')),
            ],
        ]);
        $amount = normalize_price($request->input('amount') ?? $request->input('amount_custom'));
        $service = $services->firstWhere('id', $request->input('payment_service'));

        // Assert price is positive
        if($amount <= 0)
            throw new \Exception('Amount must be positive');

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
     * Balance modification page for administrators.
     *
     * @return Response
     */
    public function modifyBalance($communityId, $economyId, $walletId) {
        // Get the user, community, find the economy and wallet
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $economy->wallets()->findOrFail($walletId);

        // User must be community manager, wallet owner is not good enough
        // TODO: improve security check, check through single function
        if(!app('perms')->evaluate(CommunityRoles::presetManager(), $community, null))
            return response(view('noPermission'));
        if(!$wallet->hasManagePermission())
            return response(view('noPermission'));

        return view('community.wallet.modifyBalance')
            ->with('economy', $economy)
            ->with('wallet', $wallet)
            ->with('currency', $wallet->currency);
    }

    /**
     * Do the wallet modification.
     *
     * @return Response
     */
    public function doModifyBalance(Request $request, $communityId, $economyId, $walletId) {
        // Get the community, find the economy and wallet
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $economy->wallets()->findOrFail($walletId);
        $currency = $wallet->currency;

        // Get initiating- and wallet user
        $init_user = barauth()->getSessionUser();
        $wallet_user = $wallet->economyMember->user;

        // User must be community manager, wallet owner is not good enough
        // TODO: improve security check, check through single function
        if(!app('perms')->evaluate(CommunityRoles::presetManager(), $community, null))
            return response(view('noPermission'));
        if(!$wallet->hasManagePermission())
            return response(view('noPermission'));

        // Validate
        $this->validate($request, [
            'modifyMethod' => 'required|in:deposit,withdraw,set',
            'amount' => ['required', ValidationDefaults::PRICE_SIGNED],
            'description' => 'nullable|string',
            'confirm' => 'accepted',
        ]);
        $amount = normalize_price($request->input('amount'));
        $description = $request->input('description');

        // Tweak amount based on modification method
        switch($request->input('modifyMethod')) {
            case 'deposit':
                break;
            case 'withdraw':
                $amount = -$amount;
                break;
            case 'set':
                $amount = -$wallet->balance + $amount;
                break;
            default:
                throw new \Exception('Unknown balance modification method');
        }
        if($amount == 0)
            throw new \Exception('Balance change amount cannot be zero');

        // Start a database transaction for the modification
        DB::transaction(function() use($init_user, $wallet_user, $economy, $wallet, $currency, $amount, $description) {
            // Create the transaction
            $transaction = Transaction::create([
                'state' => Transaction::STATE_SUCCESS,
                'description' => $description,
                'owner_id' => $wallet_user->id,
                'initiated_by_id' => $init_user->id,
                'initiated_by_other' => true,
            ]);

            // Create the magic mutation
            $mut_magic = $transaction
                ->mutations()
                ->create([
                    'economy_id' => $economy->id,
                    'mutationable_id' => 0,
                    'mutationable_type' => '',
                    'amount' => $amount,
                    'currency_id' => $currency->id,
                    'state' => Mutation::STATE_SUCCESS,
                    'owner_id' => $wallet_user->id,
                ]);
            $mut_magic->setMutationable(
                MutationMagic::create([
                    'description' => $description,
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
                    'owner_id' => $wallet_user->id,
                    'depend_on' => $mut_magic->id,
                ]);
            $mut_wallet->setMutationable(
                MutationWallet::create([
                    'wallet_id' => $wallet->id,
                ])
            );

            // Modify wallet balance
            if($amount > 0)
                $wallet->deposit($amount);
            else
                $wallet->withdraw(-$amount);
        });

        // Redirect to the payment page
        return redirect()
            ->route('community.wallet.show', [
                'communityId' => $communityId,
                'economyId' => $economyId,
                'walletId' => $walletId,
            ])
            ->with('success', __('pages.wallets.balanceModified'));
    }

    /**
     * Economy wallet quick show.
     * Redirects user to single wallet, or the index page if the user has
     * multiple wallets.
     *
     * @return Response
     */
    public function quickShow($communityId, $economyId) {
        // Get the user, community, find the economy and wallets
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $wallets = $economy_member->wallets;

        // Show single wallet or go to wallet list
        if($wallets->count() == 1)
            return redirect()
                ->route('community.wallet.show', [
                    'communityId' => $communityId,
                    'economyId' => $economyId,
                    'walletId' => $wallets->first()->id,
                ]);
        else
            return redirect()
                ->route('community.wallet.list', [
                    'communityId' => $communityId,
                    'economyId' => $economyId,
                ]);
    }

    /**
     * Economy wallet top-up page.
     *
     * @return Response
     */
    public function quickTopUp($communityId, $economyId) {
        // Get the user, community, find the economy and wallet
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $economy_member = $economy->members()->user($user)->first();
        if($economy_member == null)
            return redirect()
                ->route('community.show', ['communityId' => $communityId])
                ->with('info', __('pages.wallets.noWalletToTopUp'));

        // Get a list of preferred user currencies
        $currencies = BarController::userCurrencies($economy, $economy_member);
        if($currencies->isEmpty())
            return redirect()
                ->route('community.wallet.list', [
                    'communityId' => $communityId,
                    'economyId' => $economyId,
                ]);

        // Get or create a user wallet
        $wallet = $economy_member->getOrCreateWallet($currencies, false);
        if($wallet == null)
            return redirect()
                ->route('community.wallet.list', [
                    'communityId' => $communityId,
                    'economyId' => $economyId,
                ])
                ->with('info', __('pages.wallets.noWalletToTopUp'));

        return redirect()
            ->route('community.wallet.topUp', [
                'communityId' => $communityId,
                'economyId' => $economyId,
                'walletId' => $wallet->id,
            ]);
    }

    /**
     * Show user wallet transactions.
     *
     * @return Response
     */
    public function transactions($communityId, $economyId, $walletId) {
        // Get the community, find the economy and wallet
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $economy->wallets()->findOrFail($walletId);

        // User must have permission
        if(!$wallet->hasViewPermission())
            return response(view('noPermission'));

        $transactions = $wallet->transactions()->paginate(self::PAGINATE_ITEMS);

        return view('community.wallet.transactions')
            ->with('economy', $economy)
            ->with('wallet', $wallet)
            ->with('transactions', $transactions);
    }

    /**
     * Show wallet stats.
     *
     * @return Response
     */
    public function stats(Request $request, $communityId, $economyId, $walletId) {
        // Get the user, community, find the economy and wallet
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $wallet = $economy->wallets()->findOrFail($walletId);
        $currency = $wallet->currency;

        // User must have permission
        if(!$wallet->hasViewPermission())
            return response(view('noPermission'));

        // Select period
        switch($request->query('period')) {
            case 'week':
                $period = 'week';
                $period_from = today()->subWeek();
                break;
            case 'year':
                $period = 'year';
                $period_from = today()->subYear();
                break;
            case 'month':
            default:
                $period = 'month';
                $period_from = today()->subMonth();
        }

        // Wallet must have mutations
        if($wallet->walletMutations()->limit(1)->count() == 0)
            return redirect()
                ->route('community.wallet.show', [
                    'communityId' => $communityId,
                    'economyId' => $economyId,
                    'walletId' => $walletId,
                ])
                ->with('info', __('pages.walletStats.noStatsNoTransactions'));

        // Fetch some stats
        $transactionCount = $wallet
            ->mutations(false)
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->where('mutation.created_at', '>=', $period_from)
            ->select('transaction.id')
            ->pluck('transaction.id')
            ->unique()
            ->count();
        $productMutations = $wallet
            ->mutations(false)
            ->type(MutationProduct::class)
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->where('mutation.created_at', '>=', $period_from)
            ->groupBy('product_id')
            ->addSelect('product_id', DB::raw('SUM(quantity) AS quantity'))
            ->get();
        $productCount = $productMutations->sum('quantity');
        $uniqueProductCount = $productMutations->count();

        // Calcualte income and expenses
        $income = new MoneyAmount($currency, -1 * $wallet
            ->mutations(false)
            ->type(MutationWallet::class)
            ->select('amount')
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->where('mutation.created_at', '>=', $period_from)
            ->where('amount', '<', 0)
            ->sum('amount'));
        $expenses = new MoneyAmount($currency, 1 * $wallet
            ->mutations(false)
            ->type(MutationWallet::class)
            ->select('amount')
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->where('mutation.created_at', '>=', $period_from)
            ->where('amount', '>', 0)
            ->sum('amount'));
        $paymentIncome = new MoneyAmount($currency, 1 * $wallet
            ->mutations(false)
            ->type(MutationPayment::class)
            ->select('amount')
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->where('mutation.created_at', '>=', $period_from)
            ->sum('amount'));
        $productExpenses = new MoneyAmount($currency, -1 * $wallet
            ->mutations(false)
            ->type(MutationProduct::class)
            ->select('amount')
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->where('mutation.created_at', '>=', $period_from)
            ->sum('amount'));

        // Fetch and build chart data
        $balanceGraphData = self::chartBalanceGraph($wallet, $period_from);
        $productDistData = self::chartProductDist($wallet, $period_from);
        $buyTimeDayData = self::chartProductBuyTimeDay($wallet, $period_from);
        $buyTimeHourData = self::chartProductBuyTimeHour($wallet, $period_from);
        $buyHistogramData = self::chartProductBuyHistogram($wallet, $period_from);

        // Build smart text
        $daysActive = $buyHistogramData["datasets"][0]["data"]->count();
        $bestDay = collect($buyTimeDayData["datasets"][0]["data"])->sortDesc()->keys()->first();
        $smartText = __('pages.walletStats.smartText.main', [
            'period' => strtolower(__('pages.walletStats.period.' . $period)),
            'active-days' => trans_choice(
                'pages.walletStats.smartText.mainDays',
                $daysActive,
            ),
            'best-day' => $bestDay == null
                ? ''
                : __('pages.walletStats.smartText.mainBestDay', [
                        'day' => __('misc.days.' . $bestDay),
                    ]),
            'products' => trans_choice('pages.walletStats.smartText.productCount', $productCount),
            'products-unique' => $uniqueProductCount == 0
                ? ''
                : __('pages.walletStats.smartText.mainUniqueProducts', [
                    'unique' => trans_choice('pages.walletStats.smartText.productUniqueCount', $uniqueProductCount),
                ])
        ]);

        // Add best day if available
        $bestProduct = collect($productDistData["labels"])->first();
        if($bestProduct != null) {
            $bestProductTwo = collect($productDistData["labels"])->skip(1)->first();
            $smartText .= ' ' . __('pages.walletStats.smartText.partBestProduct', [
                'product' => $bestProduct,
                'extra' => $bestProductTwo == null
                    ? ''
                    : __('pages.walletStats.smartText.partBestProductExtra', [
                        'product' => $bestProductTwo,
                        'extra' => '',
                    ])
                ]);
        }

        return view('community.wallet.stats')
            ->with('economy', $economy)
            ->with('wallet', $wallet)
            ->with('period', $period)
            ->with('periodFrom', $period_from)
            ->with('smartText', $smartText)
            ->with('transactionCount', $transactionCount)
            ->with('expenses', $expenses)
            ->with('income', $income)
            ->with('paymentIncome', $paymentIncome)
            ->with('productExpenses', $productExpenses)
            ->with('productCount', $productCount)
            ->with('uniqueProductCount', $uniqueProductCount)
            ->with('balanceGraphData', $balanceGraphData)
            ->with('productDistData', $productDistData)
            ->with('buyTimeDayData', $buyTimeDayData)
            ->with('buyTimeHourData', $buyTimeHourData)
            ->with('buyHistogramData', $buyHistogramData);
    }

    /**
     * Get data for balance history graph.
     *
     * @param Wallet $wallet Wallet to create graph for.
     * @param Carbon|null $period_from An optional period to start the graph
     *      from. If given, the full period will be graphed. If not given, the
     *      graph will only cover the last 100 transactions.
     * @return object|null Graph data, may be null.
     */
    static function chartBalanceGraph(Wallet $wallet, $period_from = null) {
        $full_period = $period_from != null;
        $from_date = $period_from ?? today()->sub(self::WALLET_BALANCE_HISTORY_PERIOD);
        $balance = $wallet->balance;
        $day_balances = collect();

        // Get wallet mutations grouped by day
        $mutations = $wallet
            ->lastTransactions($full_period ? null : 100)
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->where('mutation.created_at', '>=', $from_date)
            ->addSelect('*')
            ->addSelect(DB::raw('CAST(mutation.created_at AS DATE) AS day'))
            ->get()
            ->groupBy('day');
        if($mutations->isEmpty())
            return null;

        // Add current day if it isn't in our datapoints
        $today = now()->toDateString();
        if(!$mutations->has($today))
            $day_balances[$today] = $balance;

        // Build list of daily balances
        $mutations
            ->each(function($day_muts, $day) use($mutations, &$balance, &$day_balances) {
                $prev_day = (new Carbon($day))->subDay()->toDateString();
                $day_balances[$day] = $balance;

                $diff = $day_muts->reduce(function($carry, $item) {
                    return round($carry - $item->amount, 2);
                }, 0);
                $balance = round($balance - $diff, 2);

                // Add diff for previous day if we have no datapoint for it
                if(!$mutations->has($prev_day))
                    $day_balances[$prev_day] = $balance;
            });

        // Add earliest day if it isn't in our datapoints and we're showing the full period
        if($full_period) {
            $earliest = $from_date->clone()->subDay()->toDateString();
            if(!$mutations->has($earliest))
                $day_balances[$earliest] = $day_balances->last();
        }

        $day_balances = $day_balances->reverse();

        // Build graph data
        if($day_balances->count() >= 4) {
            $day_balances->each(function($balance, $date) use(&$data) {
                $data['labels'][] = $date;
                $data['datasets'][0]['data'][] = $balance;
            });
        }

        return $data ?? null;
    }

    /**
     * Get data for wallet product distribution chart.
     *
     * @param Wallet $wallet The wallet to get data for.
     * @return object Produt distribution data.
     */
    static function chartProductDist(Wallet $wallet, Carbon $period_from) {
        $limit = 10;

        // Fetch product distributions
        $dist = $wallet
            ->mutations(false)
            ->type(MutationProduct::class)
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->where('mutation.created_at', '>=', $period_from)
            ->leftJoin('product', 'product.id', 'mutation_product.product_id')
            ->groupBy('product_id')
            ->addSelect('product_id', DB::raw('SUM(quantity) AS quantity'))
            ->orderBy('quantity', 'DESC')
            ->get();
        $products = Product::whereIn(
            'id',
            $dist->take($limit)->pluck('product_id')
        )->get();

        // Set labels and values data
        $data['labels'] = $dist->take($limit)->pluck('product_id')
            ->map(function($id) use($products) {
                $name = $products->firstWhere('id', $id)->name ?? __('pages.products.deletedProduct');
                // Rendering names with quotes does not work, use other quote
                return str_replace("'", "’", $name);
            });
        $data['datasets'][] = [
            'label' => __('pages.walletStats.typeProductDist.title'),
            'data' => $dist->take($limit)->pluck('quantity'),
            'borderWidth' => 1,
        ];

        // Add other item if over limit
        if($dist->count() > $limit) {
            $i = $data['labels']->count() - 1;
            $data['labels'][$i] = __('misc.other');
            $data['datasets'][0]['data'][$i] = $dist->skip($limit)->sum('quantity');
        }

        return $data;
    }

    /**
     * Get data for wallet product daily buy time.
     *
     * @param Wallet $wallet The wallet to get data for.
     * @return object Product daily buy time data.
     */
    static function chartProductBuyTimeDay(Wallet $wallet, Carbon $period_from) {
        // Fetch product buy times
        $times = $wallet
            ->mutations(false)
            ->type(MutationProduct::class)
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->where('mutation.created_at', '>=', $period_from)
            ->leftJoin('product', 'product.id', 'mutation_product.product_id')
            ->addSelect(DB::raw('DAYOFWEEK(mutation.created_at) - 2 AS day'), DB::raw('SUM(quantity) AS quantity'))
            ->groupBy('day')
            ->get();

        // Fetch product distributions, build chart data
        $data['datasets'][] = [
            'label' => __('pages.walletStats.typeProductDist.title'),
            'borderWidth' => 1,
        ];
        for($i = 0; $i < 7; $i++) {
            $data['labels'][] = now()->startOfWeek()->addDays($i)->shortDayName;
            $data['datasets'][0]['data'][] = $times->firstWhere('day', $i)->quantity ?? 0;
        }

        return $data;
    }

    /**
     * Get data for wallet product hourly buy time.
     *
     * @param Wallet $wallet The wallet to get data for.
     * @return object Product hourly buy time data.
     */
    static function chartProductBuyTimeHour(Wallet $wallet, Carbon $period_from) {
        // Fetch product buy times
        $times = $wallet
            ->mutations(false)
            ->type(MutationProduct::class)
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->where('mutation.created_at', '>=', $period_from)
            ->leftJoin('product', 'product.id', 'mutation_product.product_id')
            ->addSelect(DB::raw('HOUR(mutation.created_at) AS hour'), DB::raw('SUM(quantity) AS quantity'))
            ->groupBy('hour')
            ->get();

        // Fetch product distributions, build chart data
        $data['datasets'][] = [
            'label' => __('pages.walletStats.typeProductDist.title'),
            'borderWidth' => 1,
        ];
        for($i = 0; $i < 24; $i++) {
            $data['labels'][] = $i;
            $data['datasets'][0]['data'][] = $times->firstWhere('hour', $i)->quantity ?? 0;
        }

        return $data;
    }

    /**
     * Get data for wallet histogram.
     *
     * @param Wallet $wallet The wallet to get data for.
     * @return object Product buy time histogram.
     */
    static function chartProductBuyHistogram(Wallet $wallet, Carbon $period_from) {
        // Fetch product buy times
        $times = $wallet
            ->mutations(false)
            ->type(MutationProduct::class)
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->where('mutation.created_at', '>=', $period_from)
            ->leftJoin('product', 'product.id', 'mutation_product.product_id')
            ->addSelect(DB::raw('CAST(mutation.created_at AS DATE) AS day'), DB::raw('SUM(quantity) AS quantity'))
            ->groupBy('day')
            ->orderBy('mutation.created_at')
            ->get();

        // Set labels and values data
        $data['labels'] = $times->pluck('day');
        $data['datasets'][] = [
            'label' => __('pages.walletStats.typeProductDist.title'),
            'data' => $times->pluck('quantity'),
            'borderWidth' => 1,
        ];

        return $data;
    }
}
