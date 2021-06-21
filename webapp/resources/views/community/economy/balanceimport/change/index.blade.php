@extends('layouts.app')

@section('title', __('pages.balanceImportChange.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.balanceimport.change.index', $event);
@endphp

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
    <h2 class="ui header">@yield('title')</h2>

    @if(count($unapprovedChanges) > 0)
        <div class="ui warning message visible">
            <div class="header">@lang('pages.balanceImportChange.unapprovedChanges')</div>
            <p>@lang('pages.balanceImportChange.hasUnapprovedMustCommit')</p>
            <a href="{{ route('community.economy.balanceimport.change.approveall', [
                        // TODO: this is not efficient
                        'communityId' => $system->economy->community->human_id,
                        'economyId' => $system->economy_id,
                        'systemId' => $system->id,
                        'eventId' => $event->id,
                    ]) }}" class="ui button positive basic">
                @lang('pages.balanceImportChange.approveAll')
            </a>
        </div>
    @endif

    @if(count($unapprovedChanges) > 0)
        <div class="ui top vertical menu fluid">
            <h5 class="ui item header">
                @lang('pages.balanceImportChange.unapprovedChanges')
                ({{ count($unapprovedChanges) }})
            </h5>

            {{-- Balance import changes --}}
            @foreach($unapprovedChanges as $change)
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
        </div>
    @endif

    @if(count($approvedChanges) > 0)
        <div class="ui top vertical menu fluid">
            <h5 class="ui item header">
                @lang('pages.balanceImportChange.approvedChanges')
                ({{ count($approvedChanges) }})
            </h5>

            {{-- Balance import unapproved changes --}}
            @foreach($approvedChanges as $change)
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
        </div>
    @endif


    @if(count($unapprovedChanges) == 0)
        <div class="ui top vertical menu fluid">
            <h5 class="ui item header">
                @lang('pages.balanceImportChange.unapprovedChanges')
                ({{ count($unapprovedChanges) }})
            </h5>
            <i class="item">@lang('pages.balanceImportChange.noUnapprovedChanges')</i>
        </div>
    @endif

    <p>
        @if(perms(BalanceImportChangeController::permsManage()))
            <div class="ui buttons">
                <a href="{{ route('community.economy.balanceimport.change.create', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'systemId' => $system->id,
                        'eventId' => $event->id,
                        ]) }}"
                        class="ui button positive">
                    @lang('misc.add')
                </a>
                <a href="{{ route('community.economy.balanceimport.change.importJson', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'systemId' => $system->id,
                        'eventId' => $event->id,
                        ]) }}"
                        class="ui button primary">
                    @lang('misc.import')
                </a>
            </div>
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

        <a href="{{ route('community.economy.balanceimport.event.mailBalance', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                    'eventId' => $event->id,
                ]) }}"
                class="ui button basic">
            @lang('pages.balanceImportMailBalance.title')
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
