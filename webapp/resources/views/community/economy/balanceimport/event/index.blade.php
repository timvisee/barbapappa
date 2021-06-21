@extends('layouts.app')

@section('title', __('pages.balanceImportEvent.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.balanceimport.event.index', $system);
@endphp

@php
    use \App\Http\Controllers\BalanceImportEventController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.balanceImport.backToSystems'),
        'link' => route('community.economy.balanceimport.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui top vertical menu fluid">
        <h5 class="ui item header">
            @lang('pages.balanceImportEvent.events')
        </h5>

        {{-- Balance import events --}}
        @forelse($events as $event)
            <a class="item"
                    href="{{ route('community.economy.balanceimport.change.index', [
                        // TODO: this is not efficient
                        'communityId' => $system->economy->community->human_id,
                        'economyId' => $system->economy_id,
                        'systemId' => $system->id,
                        'eventId' => $event->id,
                    ]) }}">
                {{ $event->name }}

                <span class="sub-label">
                    @include('includes.humanTimeDiff', ['time' => $event->created_at])
                </span>
            </a>
        @empty
            <i class="item">@lang('pages.balanceImportEvent.noEvents')</i>
        @endforelse
    </div>

    <p>
        @if(perms(BalanceImportEventController::permsManage()))
            <a href="{{ route('community.economy.balanceimport.event.create', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'systemId' => $system->id,
                    ]) }}"
                    class="ui button basic positive">
                @lang('misc.add')
            </a>
        @endif

        <a href="{{ route('community.economy.balanceimport.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                ]) }}"
                class="ui button basic">
            @lang('pages.balanceImport.viewSystem')
        </a>

        <a href="{{ route('community.economy.balanceimport.exportUserList', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                ]) }}"
                class="ui button basic">
            @lang('pages.balanceImport.exportUserList')
        </a>

        <a href="{{ route('community.economy.balanceimport.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('pages.balanceImport.backToSystems')
        </a>
    </p>
@endsection
