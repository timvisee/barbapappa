@extends('layouts.app')

@section('title', __('pages.bar.tallySummary'))
@php
    $breadcrumbs = Breadcrumbs::generate('bar.tally', $bar);
    $menusection = 'bar_manage';

    use \App\Http\Controllers\BarController;
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
                'from' => $timeFrom->longRelativeDiffForHumans(null, null),
            ]):
        </p>

        @if($showingLimited)
            <div class="ui warning message">
                <span class="halflings halflings-warning-sign icon"></span>
                @lang('pages.bar.tallySummaryLimited')
            </div>
        @endif
    @endif

    <div class="ui top vertical menu fluid">
        @forelse($tallies as $userTally)
            {{-- Start item, link to user if owner is a bar member --}}
            @if($userTally['member'] != null && perms(BarController::permsManage()))
                <a class="item"
                    href="{{ route('bar.member.show', [
                        'barId' => $bar->human_id,
                        'memberId' => $userTally['member']->id,
                    ]) }}">
            @else
                <div class="item">
            @endif

            {{ $userTally['owner']?->name ?? __('misc.unknownUser') }}
            <span class="subtle">({{ $userTally['quantity'] }})</span>

            <span style="float: right; font-weight: bold;">
                @for($i = 0; $i < $userTally['quantity'] % 5; $i += 1)|@endfor
                @for($i = 0; $i < floor($userTally['quantity'] / 5); $i += 1)
                    <s>|||||</s>
                @endfor
            </span>

            {{-- End item --}}
            @if($userTally['member'] != null && perms(BarController::permsManage()))
                </a>
            @else
                </div>
            @endif

        @empty
            <i class="item">@lang('pages.bar.noPurchases')...</i>
        @endforelse
    </div>

    <p>
        @if(perms(BarController::permsManage()))
            <div class="ui floating right labeled icon dropdown button">
                <i class="dropdown icon"></i>
                @lang('misc.moreInfo')
                <div class="menu">
                    <a href="{{ route('bar.history', ['barId' => $bar->human_id]) }}"
                            class="item">
                        @lang('pages.bar.purchases')
                    </a>
                    @if(!isset($timeFrom) || !isset($timeTo))
                        <a href="{{ route('bar.summary', ['barId' => $bar->human_id]) }}"
                            class="item">
                    @else
                        <a href="{{ route('bar.summary', [
                            'barId' => $bar->human_id,
                            'time_from' => $timeFrom->toDateTimeLocalString('minute'),
                            'time_to' => $timeTo->ceilMinute()->toDateTimeLocalString('minute'),
                        ]) }}"
                                class="item">
                    @endif
                        @lang('pages.bar.purchaseSummary')
                    </a>
                </div>
            </div>
        @endif

        <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.backToBar')
        </a>
    </p>
@endsection
