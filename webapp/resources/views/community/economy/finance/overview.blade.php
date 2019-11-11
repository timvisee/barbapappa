@extends('layouts.app')

@section('title', __('pages.finance.title'))

@php
    use \App\Http\Controllers\FinanceController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.economies.backToEconomy'),
        'link' => route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.economy.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                    ]) }}">
                {{ $economy->name }}
            </a>
        </div>
    </h2>

    <h3 class="ui horizontal divider header">
        @lang('pages.wallets.title')
    </h3>
    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                {!! $walletSum->formatAmount(BALANCE_FORMAT_COLOR) !!}
            </div>
            <div class="label">@lang('pages.finance.walletSum')</div>
        </div>
    </div>

    <h3 class="ui horizontal divider header">
        @lang('pages.payments.title')
    </h3>
    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                {!! $paymentProgressingSum->formatAmount(BALANCE_FORMAT_COLOR) !!}
            </div>
            <div class="label">@lang('pages.finance.paymentsInProgress')</div>
        </div>
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
                {!! $member['balance']->formatAmount(BALANCE_FORMAT_LABEL) !!}

                @if($member['member']->user_id == null)
                    <span class="sub-label">
                        @lang('pages.finance.fromBalanceImport')
                    </span>
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
