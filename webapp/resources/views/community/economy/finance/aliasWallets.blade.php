@extends('layouts.app')

@section('title', __('pages.finance.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.finance.overview', $economy);
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui four item menu">
        <a href="{{ route('community.economy.finance.overview', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item">@lang('pages.finance.overview.title')</a>
        <a href="{{ route('community.economy.finance.members', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item">@lang('pages.finance.members.title')</a>
        <a href="{{ route('community.economy.finance.aliasWallets', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item active">@lang('pages.finance.aliasWallets.title')</a>
        <a href="{{ route('community.economy.finance.imports', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item">@lang('pages.finance.imports.title')</a>
    </div>

    <div class="ui divider hidden"></div>

    <p>@lang('pages.finance.aliasWallets.description')</p>

    <div class="ui fluid accordion">
        <div class="title">
            <i class="dropdown icon"></i>
            @lang('pages.finance.howToSettle')
        </div>
        <div class="content">
            <div class="ui info message">
                <span class="halflings halflings-info-sign icon"></span>
                @lang('pages.finance.aliasWallets.howToSettle')
            </div>
        </div>
    </div>

    <h3 class="ui horizontal divider header">
        @lang('pages.finance.aliasWallets.title')
    </h3>

    <div class="ui one small statistics">
        @if($settled)
            <div class="statistic green">
                <div class="value">
                    <span class="halflings halflings-ok" title="@lang('general.yes')"></span>
                </div>
                <div class="label">@lang('pages.finance.aliasWallets.settled')</div>
            </div>
        @else
            <div class="statistic red">
                <div class="value">
                    <span class="halflings halflings-remove" title="@lang('general.no')"></span>
                </div>
                <div class="label">@lang('pages.finance.aliasWallets.settled')</div>
            </div>
        @endif
    </div>

    @if($cumulative != null)
        <br>
        <div class="ui one small statistics">
            <div class="statistic">
                <div class="value">
                    {!! $cumulative->formatAmount(BALANCE_FORMAT_COLOR) !!}
                </div>
                <div class="label">@lang('pages.finance.outstandingBalance')</div>
            </div>
        </div>
    @endif

    <div class="ui divider hidden"></div>

    @if(!$negatives->isEmpty())
        <div class="ui vertical menu fluid">
            <h5 class="ui item header">
                @lang('pages.finance.aliasWallets.aliasesNegativeBalance')
                ({{ count($negatives) }})
            </h5>
            @foreach($negatives as $balance)
                <div class="item">
                    {{ $balance['member']->name }}
                    {!! $balance['balance']->formatAmount(BALANCE_FORMAT_LABEL) !!}
                </div>
            @endforeach
        </div>
    @endif
    @if(!$positives->isEmpty())
        <div class="ui vertical menu fluid">
            <h5 class="ui item header">
                @lang('pages.finance.aliasWallets.aliasesPositiveBalance')
                ({{ count($positives) }})
            </h5>
            @foreach($positives as $balance)
                <div class="item">
                    {{ $balance['member']->name }}
                    {!! $balance['balance']->formatAmount(BALANCE_FORMAT_LABEL) !!}
                </div>
            @endforeach
        </div>
    @endif

    <div class="ui divider hidden"></div>

    <p>
        <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('pages.economies.backToEconomy')
        </a>
    </p>
@endsection
