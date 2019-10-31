@extends('layouts.app')

@section('title', __('pages.balanceImport.title'))

@php
    use \App\Http\Controllers\BalanceImportSystemController;

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

    <div class="ui top vertical menu fluid">
        <h5 class="ui item header">
            @lang('pages.balanceImport.systems')
        </h5>

        {{-- Balance import systems --}}
        @forelse($systems as $system)
            <a class="item"
                    href="{{ route('community.economy.balanceimport.show', [
                        // TODO: this is not efficient
                        'communityId' => $system->economy->community->human_id,
                        'economyId' => $system->economy_id,
                        'systemId' => $system->id,
                    ]) }}">
                {{ $system->name }}

                {{-- {1{-- TODO: show some other stat here --}1} --}}
                {{-- <span class="sub-label"> --}}
                {{--     @include('includes.humanTimeDiff', ['time' => $service->updated_at ?? $service->created_at]) --}}
                {{-- </span> --}}
            </a>
        @empty
            <i class="item">@lang('pages.balanceImport.noSystems')</i>
        @endforelse
    </div>

    <p>
        @if(perms(BalanceImportSystemController::permsManage()))
            <a href="{{ route('community.economy.balanceimport.create', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                    class="ui button basic positive">
                @lang('misc.add')
            </a>
        @endif

        <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('pages.economies.backToEconomy')
        </a>
    </p>
@endsection
