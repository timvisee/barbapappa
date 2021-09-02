@extends('layouts.app')

@section('title', __('account.sessions'))
@php
    $breadcrumbs = Breadcrumbs::generate('bar.kiosk.sessions.index', $bar);
    $menusection = 'bar_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.bar.kioskSessionsDescription')</p>

    <div class="ui top vertical menu fluid">
        <h5 class="ui item header">
            @lang('account.activeSessions') ({{ $activeSessions->count() }})
        </h5>

        @forelse($activeSessions as $session)
            <a class="item"
                    href="{{ route('bar.kiosk.sessions.show', [
                        'barId' => $bar->human_id,
                        'sessionId' => $session->id,
                    ]) }}">

                @if(($label = $session->describe(true, true)) != null)
                    {{ $label }}
                @else
                    <i>@lang('misc.unknown')</i>
                @endif

                <span class="sub-label">
                    {{ strtolower(__('misc.started')) }}
                    @include('includes.humanTimeDiff', ['time' => $session->created_at])
                </span>
            </a>
        @empty
            <i class="item">@lang('account.noSessions')...</i>
        @endforelse
    </div>

    <a class="ui negative button" href="{{ route('bar.kiosk.sessions.expireAll', [
                'barId' => $bar->human_id
            ]) }}">
        @lang('account.expireAllSessions')
    </a>

    <div class="ui top vertical menu fluid">
        <h5 class="ui item header">
            @lang('account.expiredSessions') ({{ $expiredSessions->count() }})
        </h5>

        @forelse($expiredSessions as $session)
            <a class="item"
                    href="{{ route('bar.kiosk.sessions.show', [
                        'barId' => $bar->human_id,
                        'sessionId' => $session->id,
                    ]) }}">

                @if(($label = $session->describe(true, true)) != null)
                    {{ $label }}
                @else
                    <i>@lang('misc.unknown')</i>
                @endif

                <span class="sub-label">
                    {{ strtolower(__('misc.expired')) }}
                    @include('includes.humanTimeDiff', ['time' => $session->expire_at])
                </span>
            </a>
        @empty
            <i class="item">@lang('account.noSessions')...</i>
        @endforelse
    </div>

    <a href="{{ route('bar.manageKiosk', ['barId' => $bar->human_id]) }}"
            class="ui button basic">
        @lang('pages.bar.backToBar')
    </a>
@endsection

