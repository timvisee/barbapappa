@extends('layouts.app')

@section('title', __('pages.balanceImportChange.title'))

@php
    use \App\Http\Controllers\BalanceImportChangeController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.balanceImportEvent.backToEvents'),
        'link' => route('community.economy.balanceimport.event.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'systemId' => $system->id,
        ]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.economy.balanceimport.event.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'systemId' => $system->id,
                        'eventId' => $event->id,
                    ]) }}">
                {{ $event->name }}
            </a>
            @lang('misc.in')
            <a href="{{ route('community.economy.balanceimport.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'systemId' => $system->id,
                    ]) }}">
                {{ $system->name }}
            </a>
            @lang('misc.in')
            <a href="{{ route('community.economy.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                    ]) }}">
                {{ $economy->name }}
            </a>
        </div>
    </h2>

    @if(count($unacceptedChanges) > 0)
        <div class="ui warning message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('pages.balanceImportChange.hasUnacceptedMustCommit')
        </div>
    @endif

    <div class="ui top vertical menu fluid">
        <h5 class="ui item header">
            @lang('pages.balanceImportChange.unacceptedChanges')
        </h5>

        {{-- Balance import changes --}}
        @forelse($unacceptedChanges as $change)
            <a class="item"
                    href="{{ route('community.economy.balanceimport.change.show', [
                        // TODO: this is not efficient
                        'communityId' => $system->economy->community->human_id,
                        'economyId' => $system->economy_id,
                        'systemId' => $system->id,
                        'eventId' => $event->id,
                        'changeId' => $change->id,
                    ]) }}">
                {{ $change->alias->name }}

                {!! $change->formatAmount(BALANCE_FORMAT_LABEL) !!}

                {{-- {1{-- TODO: add nice sub label here --}1} --}}
                {{-- <span class="sub-label"> --}}
                {{--     @include('includes.humanTimeDiff', ['time' => $event->created_at]) --}}
                {{-- </span> --}}
            </a>
        @empty
            <i class="item">@lang('pages.balanceImportChange.noUnacceptedChanges')</i>
        @endforelse

        @if(count($acceptedChanges) > 0)
            <h5 class="ui item header">
                @lang('pages.balanceImportChange.acceptedChanges')
            </h5>

            {{-- Balance import unaccepted changes --}}
            @foreach($acceptedChanges as $change)
                <a class="item"
                        href="{{ route('community.economy.balanceimport.change.show', [
                            // TODO: this is not efficient
                            'communityId' => $system->economy->community->human_id,
                            'economyId' => $system->economy_id,
                            'systemId' => $system->id,
                            'eventId' => $event->id,
                            'changeId' => $change->id,
                        ]) }}">
                    {{ $change->alias->name }}

                    {!! $change->formatAmount(BALANCE_FORMAT_LABEL) !!}

                    {{-- {1{-- TODO: add nice sub label here --}1} --}}
                    {{-- <span class="sub-label"> --}}
                    {{--     @include('includes.humanTimeDiff', ['time' => $event->created_at]) --}}
                    {{-- </span> --}}
                </a>
            @endforeach
        @endif
    </div>

    <p>
        @if(perms(BalanceImportChangeController::permsManage()))
            <a href="{{ route('community.economy.balanceimport.change.create', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'systemId' => $system->id,
                    'eventId' => $event->id,
                    ]) }}"
                    class="ui button basic positive">
                @lang('misc.import')
            </a>
        @endif

        <a href="{{ route('community.economy.balanceimport.event.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                    'eventId' => $event->id,
                ]) }}"
                class="ui button basic">
            @lang('pages.balanceImportEvent.viewEvent')
        </a>

        <a href="{{ route('community.economy.balanceimport.event.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'systemId' => $system->id,
        ]) }}"
                class="ui button basic">
            @lang('pages.balanceImportEvent.backToEvents')
        </a>
    </p>
@endsection
