@extends('layouts.app')

@section('title', __('account.sessionDetails'))
@php
    $breadcrumbs = Breadcrumbs::generate('bar.kiosk.sessions', $bar);
    $menusection = 'bar_manage';
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
            @if(($description = $session->describe(false, false)) != null)
                <tr>
                    <td>@lang('misc.description')</td>
                    <td>
                        {{ $description }}
                    </td>
                </tr>
            @endif
            <tr>
                <td>@lang('misc.createdBy')</td>
                <td>{{ $session->user->name }}</td>
            </tr>
            <tr>
                <td>@lang('misc.ip')</td>
                <td><code class="literal">{{ $session->created_ip }}</code></td>
            </tr>
            <tr>
                <td>@lang('misc.userAgent')</td>
                @if($session->created_user_agent)
                    <td><code class="literal">{{ $session->created_user_agent }}</code></td>
                @else
                    <td><i>@lang('misc.unknown')</i></td>
                @endif
            </tr>
            <tr>
                <td>@lang('misc.firstSeen')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $session->created_at])</td>
            </tr>
            @if($session->expire_at != null)
                <tr>
                    <td>@lang('misc.expiry')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $session->expire_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(!$session->isExpired())
        {!! Form::open(['action' => ['KioskSessionController@doExpire', 'barId' => $bar->human_id, 'sessionId' => $session->id], 'method' => 'DELETE', 'class' => 'ui inline form']) !!}
            <button class="ui negative button" type="submit">@lang('account.expireNow')</button>
        {!! Form::close() !!}
    @endif

    <a href="{{ route('bar.kiosk.sessions', ['barId' => $bar->human_id]) }}"
            class="ui button basic">
        @lang('account.backToSessions')
    </a>
@endsection

