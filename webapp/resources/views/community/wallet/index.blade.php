@extends('layouts.app')

@section('title', __('pages.wallets.walletEconomies'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.wallet.index', $community);
    $menusection = 'community';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.wallets.economySelectDescription')</p>

    <div class="ui vertical menu fluid">
        @forelse($economies as $economy)
            <a href="{{ route('community.wallet.list', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                    ]) }}" class="item">
                {{ $economy->name }}
                {!! $economy->formatUserBalance(BALANCE_FORMAT_LABEL) !!}

                <span class="sub-label">
                    {{ trans_choice('pages.wallets.#wallets', $economy->user_wallet_count) }}
                </span>
            </a>
        @empty
            <div class="item">
                <i>@lang('pages.economies.noEconomies')</i>
            </div>
        @endforelse
    </div>

    <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.goTo')
    </a>
@endsection
