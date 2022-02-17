@extends('layouts.app')

@section('title', __('pages.finance.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.finance.overview', $economy);
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui four item menu">
        <a href="{{ route('community.economy.finance.overview', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item active">@lang('pages.finance.overview.title')</a>
        <a href="{{ route('community.economy.finance.members', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item">@lang('pages.finance.members.title')</a>
        <a href="{{ route('community.economy.finance.aliasWallets', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item">@lang('pages.finance.aliasWallets.title')</a>
        <a href="{{ route('community.economy.finance.imports', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item">@lang('pages.finance.imports.title')</a>
    </div>

    <p>@lang('pages.finance.description')</p>

    <h3 class="ui horizontal divider header">
        @lang('pages.finance.members.title')
    </h3>
    <div class="ui one small statistics">
        <a class="statistic" href="{{ route('community.economy.finance.members', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}">
            <div class="value">
                {!! $membersCumulative?->formatAmount(BALANCE_FORMAT_COLOR) ?? 0 !!}
            </div>
            <div class="label">@lang('pages.finance.cumulativeBalance')</div>
        </a>
    </div>
    <br>
    <div class="ui one small statistics">
        <a href="{{ route('community.economy.payment.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="statistic">
            <div class="value">
                {!! $paymentProgressingSum?->formatAmount(BALANCE_FORMAT_COLOR, ['neutral' => $paymentProgressingSum->amount != 0]) ?? 0 !!}
            </div>
            <div class="label">@lang('pages.finance.paymentsInProgress')</div>
        </a>
    </div>

    <h3 class="ui horizontal divider header">
        @lang('misc.totals')
    </h3>
    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                {!! $totalCumulative?->formatAmount(BALANCE_FORMAT_COLOR) ?? 0 !!}
            </div>
            <div class="label">@lang('pages.finance.memberAndOutstandingCumulative')</div>
        </div>
    </div>
    <br>
    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                {!! $outstandingCumulative?->formatAmount(BALANCE_FORMAT_COLOR) ?? 0 !!}
            </div>
            <div class="label">@lang('pages.finance.outstandingCumulative')</div>
        </div>
    </div>


    <h3 class="ui horizontal divider header">
        @lang('pages.finance.aliasWallets.title')
    </h3>
    <div class="ui one small statistics">
        <a class="statistic {{ $openWalletsResolved ? 'green' : 'red' }}" href="{{ route('community.economy.finance.aliasWallets', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}">
            @if($openWalletsResolved)
                <div class="value">
                    <span class="halflings halflings-ok" title="@lang('general.yes')"></span>
                </div>
            @else
                <div class="value">
                    <span class="halflings halflings-remove" title="@lang('general.no')"></span>
                </div>
            @endif
            <div class="label">@lang('pages.finance.aliasWallets.resolved')</div>
        </a>
    </div>

    <h3 class="ui horizontal divider header">
        @lang('pages.finance.imports.title')
    </h3>
    <div class="ui one small statistics">
        <a class="statistic {{ $importResolved ? 'green' : 'red' }}" href="{{ route('community.economy.finance.imports', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}">
            @if($importResolved)
                <div class="value">
                    <span class="halflings halflings-ok" title="@lang('general.yes')"></span>
                </div>
            @else
                <div class="value">
                    <span class="halflings halflings-remove" title="@lang('general.no')"></span>
                </div>
            @endif
            <div class="label">@lang('pages.finance.imports.resolved')</div>
        </div>
    </div>

    <div class="ui divider hidden"></div>

    <p>
        <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('pages.economies.backToEconomy')
        </a>
    </p>
@endsection
