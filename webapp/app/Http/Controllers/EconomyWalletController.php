<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Mutation;
use App\Models\MutationMagic;
use App\Models\MutationWallet;

class EconomyWalletController extends Controller {

    /**
     * Economy wallet operations overview page.
     *
     * @return Response
     */
    public function overview($communityId, $economyId) {
        // Get the user, community, find the products
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // TODO: show user wallet count

        return view('community.economy.wallets.overview')
            ->with('economy', $economy);
    }

    /**
     * Zero all wallets page.
     *
     * @return Response
     */
    public function zeroWallets($communityId, $economyId) {
        // Get the user, community, find the products
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Redirect back and show warning if there are no wallets to zero
        $hasWallets = $economy->wallets()->limit(1)->count() > 0;
        if(!$hasWallets)
            return redirect()
                ->route('community.economy.show', ['communityId' => $communityId, 'economyId' => $economyId])
                ->with('info', __('pages.economies.noWalletsInEconomy'));

        return view('community.economy.wallets.zeroWallets')
            ->with('economy', $economy);
    }

    /**
     * Do zero all wallets page.
     *
     * @return Response
     */
    public function doZeroWallets(Request $request, $communityId, $economyId) {
        // Get the user, community, find the products
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Validate
        $this->validate($request, [
            'description' => 'nullable|string',
            'confirm_text' => 'required|string|in:' . __('pages.economies.zeroAllWalletsConfirmText'),
            'confirm' => 'accepted',
        ]);
        $description = $request->input('description');

        // Zero all user wallets in transaction
        DB::transaction(function() use($user, $economy, $description) {
            foreach($economy->wallets as $wallet) {
                // Skip if wallet is zero already
                if($wallet->getMoneyAmount()->isZero())
                    continue;

                // Calculate mutation amount
                $amount = -$wallet->balance;
                if($amount == 0)
                    throw new \Exception('Failed to reset wallet balance, mutation amount cannot be zero');

                // Get wallet user to set in mutations
                $wallet_user_id = null;
                if($wallet->economy_member != null && $wallet->economy_member->user != null)
                    $wallet_user_id = $wallet->economy_member->user->id;

                // Create the transaction
                $transaction = Transaction::create([
                    'state' => Transaction::STATE_SUCCESS,
                    'description' => $description,
                    'owner_id' => $wallet_user_id,
                    'initiated_by_id' => $user->id,
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
                        'currency_id' => $wallet->currency_id,
                        'state' => Mutation::STATE_SUCCESS,
                        'owner_id' => $wallet_user_id,
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
                        'currency_id' => $wallet->currency_id,
                        'state' => Mutation::STATE_SUCCESS,
                        'owner_id' => $wallet_user_id,
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
            }
        });

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.economy.wallets.overview', ['communityId' => $communityId, 'economyId' => $economyId])
            ->with('success', __('pages.economies.walletsZeroed'));
    }

    /**
     * Delete all wallets page.
     *
     * @return Response
     */
    public function deleteWallets($communityId, $economyId) {
        // Get the user, community, find the products
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Redirect back and show warning if there are no wallets to delete
        $hasWallets = $economy->wallets()->limit(1)->count() > 0;
        if(!$hasWallets)
            return redirect()
                ->route('community.economy.show', ['communityId' => $communityId, 'economyId' => $economyId])
                ->with('info', __('pages.economies.noWalletsInEconomy'));

        // There cannot be a wallet with non-zero balance
        $nonZeroWallet = $economy
            ->wallets
            ->contains(function($wallet) {
                return !$wallet->getMoneyAmount()->isZero();
            });
        if($nonZeroWallet)
            return redirect()
                ->route('community.economy.wallets.overview', ['communityId' => $communityId, 'economyId' => $economyId])
                ->with('error', __('pages.economies.cannotDeleteWalletsNonZero'));

        return view('community.economy.wallets.deleteWallets')
            ->with('economy', $economy);
    }

    /**
     * Do delete all wallets page.
     *
     * @return Response
     */
    public function doDeleteWallets(Request $request, $communityId, $economyId) {
        // Get the user, community, find the products
        $user = barauth()->getUser();
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Validate
        $this->validate($request, [
            'confirm' => 'accepted',
        ]);

        // There cannot be a wallet with non-zero balance
        $nonZeroWallet = $economy
            ->wallets
            ->contains(function($wallet) {
                return !$wallet->getMoneyAmount()->isZero();
            });
        if($nonZeroWallet)
            return redirect()
                ->route('community.economy.wallets.overview', ['communityId' => $communityId, 'economyId' => $economyId])
                ->with('error', __('pages.economies.cannotDeleteWalletsNonZero'));

        // Delete all wallets
        DB::transaction(function() use($economy) {
            $economy->wallets()->delete();
        });

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.economy.wallets.overview', ['communityId' => $communityId, 'economyId' => $economyId])
            ->with('success', __('pages.economies.walletsDeleted'));
    }

    /**
     * The permission required for managing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        return EconomyController::permsManage();
    }
}
