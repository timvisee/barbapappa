@extends('layouts.app')

@section('title', __('pages.bar.purchaseSummary'))
@php
    $breadcrumbs = Breadcrumbs::generate('bar.summary', $bar);
    $menusection = 'bar_manage';
@endphp

@section('content')
    <h2 class="ui header bar-header">
        @yield('title')
    </h2>

    <p>@lang('pages.bar.purchaseSummaryDescription')</p>

    @if($summary->isNotEmpty())
        <p>
            @lang('pages.bar.purchaseSummaryDescriptionSum', [
                'quantity' => $quantity,
                'amount' => !$amount->isZero() ? $amount->formatAmount(BALANCE_FORMAT_COLOR) : 0,
                'from' => $timeFrom->longAbsoluteDiffForHumans(null, null),
                'to' => $timeTo->longRelativeDiffForHumans(null, null),
            ]):
        </p>

        @if($showingLimited)
            <div class="ui warning message">
                <span class="halflings halflings-warning-sign icon"></span>
                @lang('pages.bar.purchaseSummaryLimited')
            </div>
        @endif
    @endif

    @forelse($summary as $userSummary)
        <div class="ui top vertical menu fluid">

        <h5 class="ui item header">
            {{ $userSummary['owner']?->name }}

            {{-- Relative delay --}}
            <span class="subtle">
                &nbsp;&middot;&nbsp;
                @include('includes.humanTimeDiff', [
                    'time' => $userSummary['oldestUpdated'],
                    'absolute' => true,
                    'short' => true,
                ])
                @if($userSummary['oldestUpdated'] != $userSummary['newestUpdated'])
                    @lang('misc.to')
                    @include('includes.humanTimeDiff', [
                        'time' => $userSummary['newestUpdated'],
                        'absolute' => true,
                        'short' => true,
                    ])
                @endif
            </span>

            {!! $userSummary['amount']->formatAmount(BALANCE_FORMAT_LABEL, [
                'color' => true,
            ]) !!}
        </h5>

        @foreach($userSummary['products'] as $userProducts)
            {{-- Start item, link to user if owner is a bar member --}}
            @if($userSummary['member'] != null)
                <a class="item"
                    href="{{ route('bar.member.show', [
                        'barId' => $bar->human_id,
                        'memberId' => $userSummary['member']->id,
                    ]) }}">
            @else
                <div class="item">
            @endif

            @if($userProducts['quantity'] != 1)
                <span class="subtle">{{ $userProducts['quantity'] }}×</span>
            @endif

            {{ $userProducts['name'] }}
            {!! $userProducts['amount']->formatAmount(BALANCE_FORMAT_LABEL, [
                'color' => false,
            ]) !!}

            <span class="sub-label">
                {{-- Icon for delayed purchases --}}
                @if($userProducts['anyDelayed'])
                    <span class="halflings halflings-hourglass"></span>
                @endif

                {{-- Icon for kiosk purchases --}}
                @if($userProducts['anyInitiatedByKiosk'])
                    <span class="halflings halflings-shopping-cart"></span>
                @endif
            </span>

            {{-- End item --}}
            @if($userSummary['member'] != null)
                </a>
            @else
                </div>
            @endif
        @endforeach

        </div>

    @empty
        <p>@lang('pages.bar.noPurchases')...</p>
    @endforelse

    <p>
        <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.backToBar')
        </a>
    </p>
@endsection
