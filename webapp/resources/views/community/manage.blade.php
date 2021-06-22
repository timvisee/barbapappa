@extends('layouts.app')

@section('title', __('misc.managementHub'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.manage', $community);
    $menusection = 'community_manage';

    use App\Http\Controllers\AppController;
    use App\Http\Controllers\BarController;
    use App\Http\Controllers\BunqAccountController;
    use App\Http\Controllers\CommunityController;
    use App\Http\Controllers\CommunityMemberController;
    use App\Http\Controllers\EconomyController;
    use App\Http\Controllers\CurrencyController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {{-- Checklist --}}
    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('pages.community.checklist')</h5>
        @if(perms(EconomyController::permsManage()))
            <a href="{{ route('community.economy.create', ['communityId' => $community->human_id]) }}" class="item">
                @if($hasEconomy)
                    <div class="ui green small label">
                        <span class="halflings halflings-ok"></span>
                    </div>
                @else
                    <div class="ui red small label">
                        <span class="halflings halflings-remove"></span>
                    </div>
                @endif
                1. @lang('pages.economies.createEconomy')
            </a>
        @else
            <div class="item disabled">
                @if($hasEconomy)
                    <div class="ui green small label">
                        <span class="halflings halflings-ok"></span>
                    </div>
                @else
                    <div class="ui red small label">
                        <span class="halflings halflings-remove"></span>
                    </div>
                @endif
                1. @lang('pages.economies.createEconomy')
            </div>
        @endif
        @if($hasEconomy && perms(CurrencyController::permsManage()))
            <a href="{{ route('community.economy.currency.create', [
                        'communityId' => $community->human_id,
                        'economyId' => $firstEconomy->id,
                    ]) }}" class="item">
                @if($hasCurrency)
                    <div class="ui green small label">
                        <span class="halflings halflings-ok"></span>
                    </div>
                @else
                    <div class="ui red small label">
                        <span class="halflings halflings-remove"></span>
                    </div>
                @endif
                2. @lang('pages.currencies.createCurrency')
            </a>
        @else
            <div class="item disabled">
                @if($hasCurrency)
                    <div class="ui green small label">
                        <span class="halflings halflings-ok"></span>
                    </div>
                @else
                    <div class="ui red small label">
                        <span class="halflings halflings-remove"></span>
                    </div>
                @endif
                2. @lang('pages.currencies.createCurrency')
            </div>
        @endif
        @if($hasCurrency && perms(BarController::permsCreate()))
            <a href="{{ route('bar.create', ['communityId' => $community->human_id]) }}" class="item">
                @if($hasBar)
                    <div class="ui green small label">
                        <span class="halflings halflings-ok"></span>
                    </div>
                @else
                    <div class="ui red small label">
                        <span class="halflings halflings-remove"></span>
                    </div>
                @endif
                3. @lang('pages.bar.createBar')
            </a>
        @else
            <div class="item disabled">
                @if($hasBar)
                    <div class="ui green small label">
                        <span class="halflings halflings-ok"></span>
                    </div>
                @else
                    <div class="ui red small label">
                        <span class="halflings halflings-remove"></span>
                    </div>
                @endif
                3. @lang('pages.bar.createBar')
            </div>
        @endif
    </div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.community')</h5>
        @if(perms(CommunityController::permsAdminister()))
            <a href="{{ route('community.edit', ['communityId' => $community->human_id]) }}" class="item">
                @lang('pages.community.editCommunity')
            </a>
        @else
            <div class="item disabled">@lang('pages.community.editCommunity')</div>
        @endif
        @if(perms(CommunityController::permsAdminister()))
            <a href="{{ route('community.delete', ['communityId' => $community->human_id]) }}" class="item">
                @lang('pages.community.deleteCommunity')
            </a>
        @else
            <div class="item disabled">@lang('pages.community.deleteCommunity')</div>
        @endif
    </div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.assets')</h5>
        @if(perms(EconomyController::permsView()))
            <a href="{{ route('community.economy.index', ['communityId' => $community->human_id]) }}" class="item">
                @lang('pages.economies.title')
            </a>
        @else
            <div class="item disabled">@lang('pages.economies.title')</div>
        @endif
        @if(perms(CommunityMemberController::permsView()))
            <a href="{{ route('community.member.index', ['communityId' => $community->human_id]) }}" class="item">
                @lang('misc.members')
            </a>
        @else
            <div class="item disabled">@lang('misc.members')</div>
        @endif
        @if(perms(BunqAccountController::permsView()))
            <a href="{{ route('community.bunqAccount.index', ['communityId' => $community->human_id]) }}" class="item">
                @lang('pages.bunqAccounts.title')
            </a>
        @else
            <div class="item disabled">@lang('pages.economies.title')</div>
        @endif
    </div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.extras')</h5>
        @if(perms(CommunityController::permsManage()))
            <a href="{{ route('community.links', ['communityId' => $community->human_id]) }}" class="item">
                @lang('pages.community.links.title')
            </a>
        @else
            <div class="item disabled">@lang('pages.community.links.title')</div>
        @endif
        @if(perms(CommunityController::permsManage()))
            <a href="{{ route('community.poster.generate', ['communityId' => $community->human_id]) }}" class="item">
                @lang('pages.community.generatePoster')
            </a>
        @else
            <div class="item disabled">@lang('pages.community.generatePoster')</div>
        @endif
    </div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('pages.bars') ({{ $bars->count() }})</h5>
        @foreach($bars as $bar)
            <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}" class="item">
                {{ $bar->name }}
            </a>
        @endforeach
        @if(perms(BarController::permsCreate()))
            <a href="{{ route('bar.create', ['communityId' => $community->human_id]) }}" class="ui bottom attached button">
                @lang('pages.bar.createBar')
            </a>
        @else
            <div class="ui bottom attached button disabled">@lang('pages.bar.createBar')</div>
        @endif
    </div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.app')</h5>
        @if(perms(AppController::permsAdminister()))
            <a href="{{ route('app.manage') }}" class="item">
                @lang('pages.app.manageApp')
            </a>
        @else
            <div class="item disabled">@lang('pages.app.manageApp')</div>
        @endif
    </div>

    <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.backToCommunity')
    </a>
@endsection
