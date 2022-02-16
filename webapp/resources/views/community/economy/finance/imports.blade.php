@extends('layouts.app')

@section('title', __('pages.finance.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.finance.overview', $economy);
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui three item menu">
        <a href="{{ route('community.economy.finance.overview', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item">@lang('pages.finance.overview.title')</a>
        <a href="{{ route('community.economy.finance.users', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item">@lang('pages.finance.users.title')</a>
        <a href="{{ route('community.economy.finance.imports', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item active">@lang('pages.finance.imports.title')</a>
    </div>

    <p>@lang('pages.finance.description')</p>

    <h3 class="ui horizontal divider header">
        @lang('misc.balance')
    </h3>
    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                {!! $cumulative->formatAmount(BALANCE_FORMAT_COLOR) !!}
            </div>
            <div class="label">@lang('pages.finance.walletSum')</div>
        </div>
    </div>

    <h3 class="ui horizontal divider header">
        @lang('pages.finance.imports.aliases')
    </h3>
    <div class="ui vertical menu fluid">
        <h5 class="ui item header">
            @lang('pages.finance.imports.aliasesNegativeBalance')
            ({{ count($negatives) }})
        </h5>
        @forelse($negatives as $balance)
            <div class="item">
                {{ $balance['alias']->name }}
                {!! $balance['total']->formatAmount(BALANCE_FORMAT_LABEL) !!}
            </div>
        @empty
            <div class="item">
                <i>@lang('pages.finance.imports.noAliases')</i>
            </div>
        @endforelse
    </div>
    <div class="ui vertical menu fluid">
        <h5 class="ui item header">
            @lang('pages.finance.imports.aliasesPositiveBalance')
            ({{ count($positives) }})
        </h5>
        @forelse($positives as $balance)
            <div class="item">
                {{ $balance['alias']->name }}
                {!! $balance['total']->formatAmount(BALANCE_FORMAT_LABEL) !!}
            </div>
        @empty
            <div class="item">
                <i>@lang('pages.finance.imports.noAliases')</i>
            </div>
        @endforelse
    </div>

    <div class="ui divider hidden"></div>

    <p>
        <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('pages.economies.backToEconomy')
        </a>
    </p>
@endsection
