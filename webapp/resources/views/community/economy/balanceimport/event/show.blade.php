@extends('layouts.app')

@section('title', $event->name)
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.balanceimport.event.show', $event);
@endphp

@php
    use \App\Http\Controllers\BalanceImportEventController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('general.goBack'),
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

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>
                    <div class="ui list">
                        <div class="item">{{ $event->name }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $event->created_at])</td>
            </tr>
            @if($event->created_at != $event->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $event->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(perms(BalanceImportEventController::permsManage()))
        <p>
            <div class="ui buttons">
                <a href="{{ route('community.economy.balanceimport.event.edit', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'systemId' => $system->id,
                            'eventId' => $event->id,
                        ]) }}"
                        class="ui button secondary">
                    @lang('misc.edit')
                </a>
                <a href="{{ route('community.economy.balanceimport.event.delete', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'systemId' => $system->id,
                            'eventId' => $event->id,
                        ]) }}"
                        class="ui button negative">
                    @lang('misc.delete')
                </a>
            </div>

            <a href="{{ route('community.economy.balanceimport.change.index', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'systemId' => $system->id,
                        'eventId' => $event->id,
                    ]) }}"
                    class="ui button basic">
                @lang('pages.balanceImportChange.changes')
            </a>
        </p>
    @endif

    <p>
        <a href="{{ route('community.economy.balanceimport.event.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'systemId' => $system->id,
        ]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
