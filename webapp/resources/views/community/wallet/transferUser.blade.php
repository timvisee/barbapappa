@extends('layouts.app')

{{-- TODO: Translate! --}}
@section('title', __('pages.wallets.transferToUser'))

@section('content')
    <h2 class="ui header">
        @yield('title')
    </h2>

    <div class="ui two item menu">
        <a href="{{ route('community.wallet.transfer', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'walletId' => $wallet->id
        ]) }}"
            class="item">@lang('pages.wallets.toSelf')</a>
        <a href="{{ route(
            'community.wallet.transfer.user',
            ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]
        ) }}"
            class="item active">@lang('pages.wallets.toUser')</a>
    </div>

    <p>
        <i>@lang('general.notYetImplemented')</i>
    </p>

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
