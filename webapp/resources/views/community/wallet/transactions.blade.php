@extends('layouts.app')

@section('title', __('pages.wallets.walletTransactions'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.wallet.show', $community, $wallet);
@endphp

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
    <h2 class="ui header">@yield('title')</h2>

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
