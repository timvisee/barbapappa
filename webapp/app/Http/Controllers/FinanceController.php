<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Utils\MoneyAmount;
use App\Utils\MoneyAmountBag;

class FinanceController extends Controller {

    /**
     * Economy finance overview page.
     *
     * @return Response
     */
    public function overview($communityId, $economyId) {
        // Get the user, community, find the products
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Get the first currency, must have one
        $firstCurrency = $economy->currencies()->first();
        if($firstCurrency == null)
            return redirect()
                ->back()
                ->with('error', __('pages.currencies.noCurrencies'));

        $wallets = $economy->wallets;
        $walletSum = $economy->sumAmounts($wallets, 'balance') ?? MoneyAmount::zero($firstCurrency);
        $paymentsProgressing = $economy->payments()->inProgress()->get();
        $paymentProgressingSum = $economy->sumAmounts($paymentsProgressing, 'money') ?? MoneyAmount::zero($firstCurrency);

        // Gether balance for every member
        $members = $economy->members;
        $memberData = $members
            ->map(function($member) {
                return [
                    'member' => $member,
                    'balance' => $member->sumBalance(),
                ];
            })
            ->filter(function($data) {
                return $data['balance'] != null && $data['balance']->amount != 0;
            })
            ->sortByDesc(function($data) {
                return $data['balance']->amount;
            });

        return view('community.economy.finance.overview')
            ->with('economy', $economy)
            ->with('walletSum', $walletSum)
            ->with('paymentProgressingSum', $paymentProgressingSum)
            ->with('memberData', $memberData);
    }

    /**
     * Economy finance members page.
     *
     * @return Response
     */
    public function members($communityId, $economyId) {
        // Get the user, community, find the products
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Get wallets for registered users
        $wallets = $economy->wallets()->registered()->with('economyMember')->get();
        $cumulative = $economy->sumAmounts($wallets, 'balance');

        // Total balances
        $balances = [];
        $wallets
            ->each(function($wallet) use(&$balances) {
                if(!isset($balances[$wallet->economy_member_id]))
                    $balances[$wallet->economy_member_id] = [
                        'member' => $wallet->economyMember,
                        'balance' => $wallet->getMoneyAmount()->toBag(),
                    ];
                else
                    $balances[$wallet->economy_member_id]['balance']
                        ->add($wallet->getMoneyAmount());
            });

        // Sort data
        $balances = collect($balances)
            ->filter(function($data) {
                return !$data['balance']->isZero();
            })
            ->map(function($balance) {
                $balance['balanceNum'] = $balance['balance']->sumAmounts()->amount;
                return $balance;
            });

        // Split balances into positives and negatives
        [$positives, $negatives] = collect($balances)
            ->partition(function($balance) {
                return $balance['balanceNum'] > 0;
            });
        $positives = $positives
            ->sortByDesc(function($item) {
                return $item['balanceNum'];
            });
        $negatives = $negatives
            ->sortBy(function($item) {
                return $item['balanceNum'];
            });

        return view('community.economy.finance.members')
            ->with('economy', $economy)
            ->with('cumulative', $cumulative)
            ->with('positives', $positives)
            ->with('negatives', $negatives);
    }

    /**
     * Economy finance alias wallets page.
     *
     * @return Response
     */
    public function aliasWallets($communityId, $economyId) {
        // Get the user, community, find the products
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);

        // Get wallets for registered users
        $wallets = $economy->wallets()->registered(false)->with('economyMember')->get();
        $cumulative = $economy->sumAmounts($wallets, 'balance');

        // Total balances
        $balances = [];
        $wallets
            ->each(function($wallet) use(&$balances) {
                if(!isset($balances[$wallet->economy_member_id]))
                    $balances[$wallet->economy_member_id] = [
                        'member' => $wallet->economyMember,
                        'balance' => $wallet->getMoneyAmount()->toBag(),
                    ];
                else
                    $balances[$wallet->economy_member_id]['balance']
                        ->add($wallet->getMoneyAmount());
            });

        // Sort data
        $balances = collect($balances)
            ->filter(function($data) {
                return !$data['balance']->isZero();
            })
            ->map(function($balance) {
                $balance['balanceNum'] = $balance['balance']->sumAmounts()->amount;
                return $balance;
            });

        // Split balances into positives and negatives
        [$positives, $negatives] = collect($balances)
            ->partition(function($balance) {
                return $balance['balanceNum'] > 0;
            });
        $positives = $positives
            ->sortByDesc(function($item) {
                return $item['balanceNum'];
            });
        $negatives = $negatives
            ->sortBy(function($item) {
                return $item['balanceNum'];
            });

        return view('community.economy.finance.aliasWallets')
            ->with('economy', $economy)
            ->with('cumulative', $cumulative)
            ->with('resolved', $positives->isEmpty() && $negatives->isEmpty())
            ->with('positives', $positives)
            ->with('negatives', $negatives);
    }

    /**
     * Economy finance imports page.
     *
     * @return Response
     */
    public function imports(Request $request, $communityId, $economyId, $systemId = null) {
        // Get the user, community, find the products
        $community = \Request::get('community');
        $economy = $community->economies()->findOrFail($economyId);
        $systems = $economy->balanceImportSystems;

        $system = null;
        if($systemId != null)
            $system = $systems->find($systemId);
        [$positives, $negatives] = [collect(), collect()];

        if($system != null) {
            // Collect balances
            $balances = [];
            $system
                ->changes()
                ->approved()
                ->committed(false)
                ->get()
                ->each(function($change) use(&$balances, $system) {
                    if(!isset($balances[$change->alias_id])) {
                        $balances[$change->alias_id] = [
                            'alias' => $change->alias,
                            'cost_sum' => new MoneyAmountBag(),
                            'balance' => new MoneyAmountBag(),
                        ];
                    }

                    if(!empty($change->cost))
                        // TODO: invert this?
                        $balances[$change->alias_id]['cost_sum']->sum(new MoneyAmount($change->currency, $change->cost));
                    if(!empty($change->balance))
                        $balances[$change->alias_id]['balance']->set(new MoneyAmount($change->currency, $change->balance));
                });

            // Total balances
            $cumulative = new MoneyAmountBag();
            $balances = collect($balances)
                ->map(function($item) use(&$cumulative) {
                    $total = $item['cost_sum']->clone()->addBag($item['balance']);
                    $item['total'] = $total;
                    $item['totalNum'] = $total->sumAmounts()->amount;
                    $cumulative->addBag($total);
                    return $item;
                })
                ->filter(function($item) {
                    return !$item['total']->isZero();
                });

            // Split balances into positives and negatives
            [$positives, $negatives] = $balances->partition(function($balance) {
                return $balance['totalNum'] > 0;
            });
            $positives = $positives
                ->sortByDesc(function($item) {
                    return $item['totalNum'];
                });
            $negatives = $negatives
                ->sortBy(function($item) {
                    return $item['totalNum'];
                });
        }

        return view('community.economy.finance.imports')
            ->with('economy', $economy)
            ->with('systems', $systems)
            ->with('system', $system)
            ->with('resolved', $positives->isEmpty() && $negatives->isEmpty())
            ->with('positives', $positives)
            ->with('negatives', $negatives)
            ->with('cumulative', $cumulative ?? null);
    }

    /**
     * The permission required for viewing.
     * @return PermsConfig The permission configuration.
     */
    public static function permsView() {
        return EconomyController::permsView();
    }
}
