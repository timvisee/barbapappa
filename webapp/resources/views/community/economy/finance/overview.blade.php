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
        @lang('pages.wallets.title')
    </h3>
    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                {!! $walletSum->formatAmount(BALANCE_FORMAT_COLOR) !!}
            </div>
            <div class="label">@lang('pages.finance.cumulativeBalance')</div>
        </div>
    </div>

    <h3 class="ui horizontal divider header">
        @lang('pages.payments.title')
    </h3>
    <div class="ui one small statistics">
        <a href="{{ route('community.economy.payment.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="statistic">
            <div class="value">
                {!! $paymentProgressingSum->formatAmount(BALANCE_FORMAT_COLOR, ['neutral' => $paymentProgressingSum->amount != 0]) !!}
            </div>
            <div class="label">@lang('pages.finance.paymentsInProgress')</div>
        </a>
    </div>

    <h3 class="ui horizontal divider header">
        @lang('misc.members')
    </h3>
    <div class="ui vertical menu fluid">
        <h5 class="ui item header">
            @lang('pages.finance.membersWithNonZeroBalance')
            ({{ count($memberData) }})
        </h5>
        @forelse($memberData as $member)
            <div class="item">
                {{ $member['member']->name }}
                @if($member['balance'])
                    {!! $member['balance']->formatAmount(BALANCE_FORMAT_LABEL) !!}

                    @unless($member['member']->user_id)
                        <span class="sub-label">
                            {{ lcfirst(__('pages.finance.noAccountImport')) }}
                        </span>
                    @endunless
                @endif
            </div>
        @empty
            <div class="item">
                <i>@lang('pages.barMembers.noMembers')</i>
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
