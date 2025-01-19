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

    <div class="ui two item menu">
        <a href="{{ route('bar.tally', ['barId' => $bar->human_id]) }}" class="item {{ !$specificPeriod ? 'active' : '' }}">@lang('misc.recent')</a>
        <a href="{{ route('bar.tally', [
            'barId' => $bar->human_id,
            'time_from' => $timeFrom->toDateTimeLocalString('minute'),
            'time_to' => $timeTo->toDateTimeLocalString('minute'),
        ]) }}" class="item {{ $specificPeriod ? 'active' : '' }}">@lang('misc.specificPeriod')</a>
    </div>

    {{-- Period input --}}
    @if($specificPeriod)
        {!! Form::open([
            'method' => 'GET',
            'class' => 'ui form'
        ]) !!}
            <div class="two fields">
                <div class="required field {{ ErrorRenderer::hasError('time_from') ? 'error' : '' }}">
                    {{ Form::label('time_from', __('misc.fromTime') . ':') }}
                    {{ Form::datetimeLocal('time_from', $timeFrom?->toDateTimeLocalString('minute'), [
                        'min' => $bar->created_at->floorDay()->toDateTimeLocalString('minute'),
                        'max' => now()->toDateTimeLocalString('minute'),
                    ]) }}
                    {{ ErrorRenderer::inline('time_from') }}
                </div>

                <div class="required field {{ ErrorRenderer::hasError('time_to') ? 'error' : '' }}">
                    {{ Form::label('time_to', __('misc.toTime') . ':') }}
                    {{ Form::datetimeLocal('time_to', ($timeTo ?? now())->toDateTimeLocalString('minute'), [
                        'min' => $bar->created_at->floorDay()->toDateTimeLocalString('minute'),
                        'max' => now()->toDateTimeLocalString('minute'),
                    ]) }}
                    {{ ErrorRenderer::inline('time_to') }}
                </div>
            </div>

            <button class="ui button blue" type="submit">@lang('misc.apply')</button>

            <div class="ui buttons">
                @if(!$bar->created_at->addDay()->isFuture())
                    <a href="{{ route('bar.tally', [
                                'barId' => $bar->id,
                                'time_from' => now()->subDay()->max($bar->created_at)->toDateTimeLocalString('minute'),
                                'time_to' => now()->toDateTimeLocalString('minute'),
                            ]) }}"
                            class="ui button">
                        @lang('pages.inventories.period.day')
                    </a>
                @endif
                @if(!$bar->created_at->addWeek()->isFuture())
                    <a href="{{ route('bar.tally', [
                                'barId' => $bar->id,
                                'time_from' => now()->subWeek()->max($bar->created_at)->toDateTimeLocalString('minute'),
                                'time_to' => now()->toDateTimeLocalString('minute'),
                            ]) }}"
                            class="ui button">
                        @lang('pages.inventories.period.week')
                    </a>
                @endif
                @if(!$bar->created_at->addMonth()->isFuture())
                    <a href="{{ route('bar.tally', [
                                'barId' => $bar->id,
                                'time_from' => now()->subMonth()->max($bar->created_at)->toDateTimeLocalString('minute'),
                                'time_to' => now()->toDateTimeLocalString('minute'),
                            ]) }}"
                            class="ui button">
                        @lang('pages.inventories.period.month')
                    </a>
                @endif
            </div>

            <div class="ui hidden divider"></div>
        {!! Form::close() !!}
    @endif

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
