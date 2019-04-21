@extends('layouts.app')

@section('title', __('pages.wallets.walletTransactions'))

@section('content')
    <h2 class="ui header">
        @yield('title')
        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.wallet.show', [
                        'communityId' => $community->id,
                        'economyId' => $economy->id,
                        'walletId' => $wallet->id,
                    ]) }}">
                {{ $wallet->name }}
            </a>
        </div>
    </h2>

    {{-- Transaction list --}}
    @include('transaction.include.list', [
        'groups' => [[
            'header' => __('pages.wallets.walletTransactions') . ' (' .  count($transactions) . ')',
            'transactions' => $transactions,
        ]],
    ])

    <p>
        <a href="{{ route('community.wallet.show', [
                    'communityId' => $community->id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id,
                ]) }}"
                class="ui button basic">
            @lang('pages.wallets.backToWallet')
        </a>
    </p>
@endsection
