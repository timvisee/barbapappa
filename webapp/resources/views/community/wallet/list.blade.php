@extends('layouts.app')

@section('title', __('pages.wallets.yourWallets'))

@php
    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.community.goTo'),
        'link' => route('community.show', ['communityId' => $community->human_id]),
        'icon' => 'undo',
    ];
    $menulinks[] = [
        'name' => __('pages.wallets.all'),
        'link' => route('community.wallet.index', ['communityId' => $community->human_id]),
        'icon' => 'wallet',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title') ({{ count($wallets) }})

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.wallet.index', ['communityId' => $community->human_id]) }}">
                {{ $community->name }}
            </a>
            @lang('misc.for')
            {{ $economy->name }}
        </div>
    </h2>

    <p>@lang('pages.wallets.description')</p>

    <div class="ui vertical menu fluid">
        {{--
            <div class="item">
                <div class="ui transparent icon input">
                    {{ Form::text('search', '', ['placeholder' => 'Search communities...']) }}
                    <i class="icon link">
                        <span class="glyphicons glyphicons-search"></span>
                    </i>
                </div>
            </div>
        --}}

        @forelse($wallets as $wallet)
            <a href="{{ route('community.wallet.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'walletId' => $wallet->id,
            ]) }}" class="item">
                {{ $wallet->name }}
                {!! $wallet->formatBalance(BALANCE_FORMAT_LABEL) !!}
            </a>
        @empty
            <div class="item">
                <i>@lang('pages.wallets.noWallets')</i>
            </div>
        @endforelse
    </div>

    {{-- TODO: check whether the user can create a wallet in any currency --}}
    <a href="{{ route('community.wallet.create', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
            class="ui button basic positive">
        @lang('misc.create')
    </a>

    <a href="{{ route('community.wallet.merge', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
            class="ui button basic">
        @lang('misc.merge')
    </a>

    <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.goTo')
    </a>
@endsection
