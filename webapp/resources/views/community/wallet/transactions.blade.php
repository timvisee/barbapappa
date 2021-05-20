@extends('layouts.app')

@section('title', __('pages.wallets.walletTransactions'))

@php
    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.wallets.backToWallet'),
        'link' => route('community.wallet.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id,
                ]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')
        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.wallet.show', [
                        'communityId' => $community->human_id,
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
            'header' => __('pages.wallets.walletTransactions'),
            'transactions' => $transactions,
        ]],
    ])
    {{ $transactions->links() }}

    <p>
        <a href="{{ route('community.wallet.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id,
                ]) }}"
                class="ui button basic">
            @lang('pages.wallets.backToWallet')
        </a>
    </p>
@endsection
