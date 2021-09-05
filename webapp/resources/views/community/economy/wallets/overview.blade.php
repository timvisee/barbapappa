@extends('layouts.app')

@section('title', __('pages.economies.walletOperations'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.wallets.overview', $economy);
    $menusection = 'community_manage';

    use App\Http\Controllers\EconomyController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    @if(perms(EconomyController::permsManage()))
        <div class="ui vertical menu fluid">
            <h5 class="ui item header">@lang('pages.wallets.title')</h5>
            <a href="{{ route('community.economy.wallets.zeroWallets', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                    class="item">
                @lang('pages.economies.zeroAllWallets')
            </a>
            <a href="{{ route('community.economy.wallets.deleteWallets', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                    class="item">
                @lang('pages.economies.deleteAllWallets')
            </a>
        </div>
    @endif

    <p>
        <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('pages.economies.backToEconomy')
        </a>
    </p>
@endsection
