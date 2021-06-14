@extends('layouts.app')

@section('title', __('account.sessions'))

@php
    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.accountPage.backToAccount'),
        'link' => route('account', ['userId' => $user->id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('account.sessionsDescription')</p>

    <div class="ui top vertical menu fluid">
        <h5 class="ui item header">
            @lang('account.activeSessions') ({{ $activeSessions->count() }})
        </h5>

        @forelse($activeSessions as $session)
            <a class="item"
                    href="{{ route('account.sessions.show', [
                        'userId' => $user->id,
                        'sessionId' => $session->id,
                    ]) }}">

                @if($session->created_ip)
                    {{ $session->created_ip }}
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

    <div class="ui top vertical menu fluid">
        <h5 class="ui item header">
            @lang('account.expiredSessions') ({{ $expiredSessions->count() }})
        </h5>

        @forelse($expiredSessions as $session)
            <a class="item"
                    href="{{ route('account.sessions.show', [
                        'userId' => $user->id,
                        'sessionId' => $session->id,
                    ]) }}">

                @if($session->created_ip)
                    {{ $session->created_ip }}
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

    <a href="{{ route('account', ['userId' => $user->id]) }}"
            class="ui button basic">
        @lang('pages.accountPage.backToAccount')
    </a>
@endsection

