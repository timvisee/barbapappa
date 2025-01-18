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

    <div class="ui hidden divider"></div>

    @forelse($summary as $userSummary)
        <div class="ui top vertical menu fluid">

        <h5 class="ui item header">
            {{ $userSummary['owner']?->name }}

            {{-- Relative delay --}}
            <span class="subtle">
                &nbsp;&middot;&nbsp;
                @include('includes.humanTimeDiff', [
                    'time' => $userSummary['newestUpdated'],
                    'absolute' => true,
                    'short' => true,
                ])
                @if($userSummary['newestUpdated'] != $userSummary['oldestUpdated'])
                    -
                    @include('includes.humanTimeDiff', [
                        'time' => $userSummary['oldestUpdated'],
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
            <a class="item"
                href="{{ route('transaction.show', [
                    'transactionId' => 0,
                ]) }}">

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
            </a>
        @endforeach

        </div>

    @empty
        <i class="item">@lang('pages.bar.noPurchases')...</i>
    @endforelse

    <p>
        <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.backToBar')
        </a>
    </p>
@endsection
