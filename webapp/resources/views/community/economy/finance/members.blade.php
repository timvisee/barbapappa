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
        <a href="{{ route('community.economy.finance.members', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item active">@lang('pages.finance.members.title')</a>
        <a href="{{ route('community.economy.finance.imports', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item">@lang('pages.finance.imports.title')</a>
    </div>

    <p>@lang('pages.finance.members.description')</p>

    @if($cumulative != null)
        <h3 class="ui horizontal divider header">
            @lang('pages.wallets.title')
        </h3>
        <div class="ui one small statistics">
            <div class="statistic">
                <div class="value">
                    {!! $cumulative->formatAmount(BALANCE_FORMAT_COLOR) !!}
                </div>
                <div class="label">@lang('pages.finance.walletSum')</div>
            </div>
        </div>
    @endif

    <h3 class="ui horizontal divider header">
        @lang('misc.members')
    </h3>
    @if(!$negatives->isEmpty())
        <div class="ui vertical menu fluid">
            <h5 class="ui item header">
                @lang('pages.finance.members.membersNegativeBalance')
                ({{ count($negatives) }})
            </h5>
            @forelse($negatives as $balance)
                <div class="item">
                    {{ $balance['member']->name }}
                    {!! $balance['balance']->formatAmount(BALANCE_FORMAT_LABEL) !!}
                </div>
            @empty
                <div class="item">
                    <i>@lang('pages.barMembers.noMembers')</i>
                </div>
            @endforelse
        </div>
    @endif
    @if(!$positives->isEmpty())
        <div class="ui vertical menu fluid">
            <h5 class="ui item header">
                @lang('pages.finance.members.membersPositiveBalance')
                ({{ count($positives) }})
            </h5>
            @forelse($positives as $balance)
                <div class="item">
                    {{ $balance['member']->name }}
                    {!! $balance['balance']->formatAmount(BALANCE_FORMAT_LABEL) !!}
                </div>
            @empty
                <div class="item">
                    <i>@lang('pages.barMembers.noMembers')</i>
                </div>
            @endforelse
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
