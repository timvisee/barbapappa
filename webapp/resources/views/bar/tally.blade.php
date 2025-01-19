@extends('layouts.app')

@section('title', __('pages.bar.tallySummary'))
@php
    $breadcrumbs = Breadcrumbs::generate('bar.tally', $bar);
    $menusection = 'bar_manage';
@endphp

@section('content')
    <h2 class="ui header bar-header">
        @yield('title')
    </h2>

    <div class="ui four item menu">
        <a href="{{ route('bar.tally', ['barId' => $bar->human_id]) }}"
            class="item {{ !isset($period) ? 'active' : '' }}">@lang('misc.recent')</a>
        <a href="{{ route('bar.tally', [
            'barId' => $bar->human_id,
            'period' => 'day',
        ]) }}" class="item {{ $period == 'day' ? 'active' : '' }}">@lang('pages.inventories.period.day')</a>
        <a href="{{ route('bar.tally', [
            'barId' => $bar->human_id,
            'period' => 'week',
        ]) }}" class="item {{ $period == 'week' ? 'active' : '' }}">@lang('pages.inventories.period.week')</a>
        <a href="{{ route('bar.tally', [
            'barId' => $bar->human_id,
            'period' => 'month',
        ]) }}" class="item {{ $period == 'month' ? 'active' : '' }}">@lang('pages.inventories.period.month')</a>
    </div>

    <p>@lang('pages.bar.tallySummaryDescription')</p>

    @if($tallies->isNotEmpty())
        <p>
            @lang('pages.bar.tallySummaryDescriptionSum', [
                'quantity' => $quantity,
                'from' => $timeFrom->longAbsoluteDiffForHumans(null, null),
                'to' => $timeTo->longRelativeDiffForHumans(null, null),
            ]):
        </p>

        @if($showingLimited)
            <div class="ui warning message">
                <span class="halflings halflings-warning-sign icon"></span>
                @lang('pages.bar.tallySummaryLimited')
            </div>
        @endif
    @endif

    @forelse($tallies as $userTally)
        <div class="ui top vertical menu fluid">

        {{-- Start item, link to user if owner is a bar member --}}
        @if($userTally['member'] != null)
            <a class="item"
                href="{{ route('bar.member.show', [
                    'barId' => $bar->human_id,
                    'memberId' => $userTally['member']->id,
                ]) }}">
        @else
            <div class="item">
        @endif

        {{ $userTally['owner']?->name }} ({{ $userTally['quantity'] }})

        <span style="float: right; font-weight: bold;">
            @for($i = 0; $i < $userTally['quantity'] % 5; $i += 1)|@endfor
            @for($i = 0; $i < floor($userTally['quantity'] / 5); $i += 1)
                <s>|||||</s>
            @endfor
        </span>

        {{-- End item --}}
        @if($userTally['member'] != null)
            </a>
        @else
            </div>
        @endif

        </div>

    @empty
        <p>@lang('pages.bar.noPurchases')...</p>
    @endforelse

    <p>
        <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.backToBar')
        </a>
    </p>
@endsection
