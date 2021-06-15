@extends('layouts.app')

@section('title', __('account.sessionDetails'))

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

    {{-- Expired state icon --}}
    <div class="ui one small statistics">
        @if($session->isExpired())
            <div class="statistic red">
                <div class="value">
                    <span class="halflings halflings-remove" title="@lang('misc.expired')"></span>
                </div>
                <div class="label">@lang('misc.state')</div>
            </div>
        @else
            <div class="statistic green">
                <div class="value">
                    <span class="halflings halflings-ok" title="@lang('misc.active')"></span>
                </div>
                <div class="label">@lang('misc.state')</div>
            </div>
        @endif
    </div>

    <div class="ui divider large hidden"></div>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.expired')</td>
                <td>{{ yesno($session->isExpired()) }}</td>
            </tr>
            <tr>
                <td>@lang('account.thisSession')</td>
                <td>{{ yesno($session->isCurrent()) }}</td>
            </tr>
            <tr>
                <td>@lang('account.thisNetwork')</td>
                <td>{{ yesno($session->isSameIp()) }}</td>
            </tr>
            <tr>
                <td>@lang('misc.ip')</td>
                <td>{{ $session->created_ip }}</td>
            </tr>
            <tr>
                <td>@lang('misc.firstSeen')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $session->created_at])</td>
            </tr>
            @if($session->created_at != $session->updated_at)
                <tr>
                    <td>@lang('misc.lastUpdated')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $session->updated_at])</td>
                </tr>
            @endif
            @if($session->expire_at != null)
                <tr>
                    <td>@lang('misc.expiry')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $session->expire_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(!$session->isExpired() && !$session->isCurrent())
        {!! Form::open(['action' => ['SessionController@doExpire', 'userId' => $user->id, 'sessionId' => $session->id], 'method' => 'DELETE', 'class' => 'ui inline form']) !!}
            <button class="ui negative button" type="submit">@lang('account.expireNow')</button>
        {!! Form::close() !!}
    @endif

    <a href="{{ route('account.sessions', ['userId' => $user->id]) }}"
            class="ui button basic">
        @lang('account.backToSessions')
    </a>
@endsection

