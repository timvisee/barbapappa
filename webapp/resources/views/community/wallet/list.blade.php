@extends('layouts.app')

@section('title', __('pages.wallets.yourWallets'))

@php
    use \App\Http\Controllers\EconomyController;
    use \App\Models\Wallet;
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title') ({{ count($wallets) }})

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.wallet.index', ['communityId' => $community->id]) }}">
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
                    <input type="text" placeholder="Search communities...">
                    <i class="icon glyphicons glyphicons-search link"></i>
                </div>
            </div>
        --}}

        @forelse($wallets as $wallet)
            {{-- TODO: link to wallet page --}}
            <a href="{{ route('community.wallet.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'walletId' => $wallet->id
            ]) }}" class="item">
                {{ $wallet->name }}
                {!! $wallet->formatBalance(BALANCE_FORMAT_LABEL); !!}
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

    {{-- TODO: only show if there are other wallet economies --}}
    <a href="{{ route('community.wallet.index', ['communityId' => $community->id]) }}"
            class="ui button basic">
        @lang('pages.wallets.all')
    </a>

    <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.goTo')
    </a>

    {{-- TODO: list transactions for the user here --}}
@endsection
