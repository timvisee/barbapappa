@extends('layouts.app')

@section('title', __('misc.managementHub'))
@php
    $breadcrumbs = Breadcrumbs::generate('bar.manage', $bar);
    $menusection = 'bar_manage';

    use App\Http\Controllers\BarController;
    use App\Http\Controllers\BarMemberController;
    use App\Http\Controllers\CommunityController;
    use App\Http\Controllers\EconomyController;
    use App\Http\Controllers\ProductController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {{-- Checklist --}}
    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('pages.bar.checklist')</h5>
        @if(perms(ProductController::permsManage()))
            <a href="{{ route('community.economy.product.create', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
            ]) }}" class="item">
                @if($hasProduct)
                    <div class="ui green small label">
                        <span class="halflings halflings-ok"></span>
                    </div>
                @else
                    <div class="ui red small label">
                        <span class="halflings halflings-remove"></span>
                    </div>
                @endif
                1. @lang('pages.products.addProducts')
            </a>
        @else
            <div class="item disabled">
                @if($hasProduct)
                    <div class="ui green small label">
                        <span class="halflings halflings-ok"></span>
                    </div>
                @else
                    <div class="ui red small label">
                        <span class="halflings halflings-remove"></span>
                    </div>
                @endif
                1. @lang('pages.products.addProducts')
            </div>
        @endif
    </div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.bar')</h5>
        @if(perms(BarController::permsAdminister()))
            <a href="{{ route('bar.edit', ['barId' => $bar->human_id]) }}" class="item">
                @lang('pages.bar.editBar')
            </a>
        @else
            <div class="item disabled">@lang('pages.bar.editBar')</div>
        @endif
        @if(perms(BarController::permsAdminister()))
            <a href="{{ route('bar.delete', ['barId' => $bar->human_id]) }}" class="item">
                @lang('pages.bar.deleteBar')
            </a>
        @else
            <div class="item disabled">@lang('pages.bar.deleteBar')</div>
        @endif
    </div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.assets')</h5>
        @if(perms(BarMemberController::permsView()))
            <a href="{{ route('bar.member.index', ['barId' => $bar->human_id]) }}" class="item">
                @lang('misc.members')
            </a>
        @else
            <div class="item disabled">@lang('misc.members')</div>
        @endif
        @if(perms(EconomyController::permsView()))
            <a href="{{ route('community.economy.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $bar->economy_id
                    ]) }}" class="item">
                @lang('pages.community.economy')
                <span class="subtle">@lang('pages.community.inCommunity')</span>
            </a>
        @else
            <div class="item disabled">
                @lang('pages.community.economy')
                <span class="subtle">@lang('pages.community.inCommunity')</span>
            </div>
        @endif
        @if(perms(ProductController::permsView()))
            <a href="{{ route('community.economy.product.index', [
                        'communityId' => $community->human_id,
                        'economyId' => $bar->economy_id
                    ]) }}" class="item">
                @lang('pages.products.title')
                <span class="subtle">@lang('pages.economies.inEconomy')</span>
            </a>
        @else
            <div class="item disabled">
                @lang('pages.products.title')
                <span class="subtle">@lang('pages.economies.inEconomy')</span>
            </div>
        @endif
    </div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.extras')</h5>
        @if(perms(BarController::permsManage()))
            <a href="{{ route('bar.history', ['barId' => $bar->human_id]) }}" class="item">
                @lang('pages.bar.purchaseHistory')
            </a>
        @else
            <div class="item disabled">@lang('pages.bar.purchaseHistory')</div>
        @endif
        @if(perms(BarController::permsManage()))
            <a href="{{ route('bar.links', ['barId' => $bar->human_id]) }}" class="item">
                @lang('pages.bar.links.title')
            </a>
        @else
            <div class="item disabled">@lang('pages.bar.links.title')</div>
        @endif
        @if(perms(BarController::permsManage()))
            <a href="{{ route('bar.kiosk.start', ['barId' => $bar->human_id]) }}" class="item">
                @lang('pages.bar.startKiosk')
            </a>
        @else
            <div class="item disabled">@lang('pages.bar.startKiosk')</div>
        @endif
        @if(perms(BarController::permsManage()))
            <a href="{{ route('bar.kiosk.sessions', ['barId' => $bar->human_id]) }}" class="item">
                @lang('pages.bar.kioskSessions')
            </a>
        @else
            <div class="item disabled">@lang('pages.bar.kioskSessions')</div>
        @endif
        @if(perms(BarController::permsManage()))
            <a href="{{ route('bar.poster.generate', ['barId' => $bar->human_id]) }}" class="item">
                @lang('pages.bar.generatePoster')
            </a>
        @else
            <div class="item disabled">@lang('pages.bar.generatePoster')</div>
        @endif
    </div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.community')</h5>
        @if(perms(CommunityController::permsManage()))
            <a href="{{ route('community.manage', ['communityId' => $bar->community->human_id]) }}" class="item">
                @lang('pages.community.manageCommunity')
            </a>
        @else
            <div class="item disabled">@lang('pages.community.manageCommunity')</div>
        @endif
    </div>

    <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
            class="ui button basic">
        @lang('pages.bar.backToBar')
    </a>
@endsection
