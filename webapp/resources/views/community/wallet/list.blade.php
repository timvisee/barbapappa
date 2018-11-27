@extends('layouts.app')

@php
    use \App\Models\Wallet;
@endphp

@section('content')
    <h2 class="ui header">
        @lang('pages.wallets.yourWallets') ({{ count($wallets) }})
        <div class="sub header">
            in
            <a href="{{ route('community.show', ['communityId' => $community->id]) }}">
                {{ $community->name }}
            </a>
            /
            <a href="{{ route('community.economy.show', ['communityId' => $community->id, 'economyId' => $economy->id]) }}">
                {{ $economy->name }}
            </a>
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
                {!! $wallet->formatBalance(null, Wallet::BALANCE_LABEL); !!}
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

    <a href="{{ route('community.wallet.index', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('general.goBack')
    </a>
@endsection
