@extends('layouts.app')

@section('title', __('pages.noPermission.title'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.noPermission.description')</p>

    <img src="{{ asset('img/no-permission.png') }}" style="width: 150px; height: 221px;" />

    <br />

    @unless(barauth()->isAuth())
        <div class="ui info message visible">
            <div class="header">@lang('pages.noPermission.notLoggedIn')</div>
            <p>@lang('pages.noPermission.notLoggedInDescription')</p>
        </div>
    @endif

    <br />

    @php
        // Get the home route name to use based on the session
        $homeRoute = barauth()->isAuth() ? 'dashboard' : 'index';

        // Determine whether we have a previous URL
        $hasPrevious = url()->previous() != url()->current();
    @endphp

    @unless(barauth()->isAuth())
        {{-- TODO: redirect to this page after login --}}
        <a class="ui button primary"
                href="{{ route('login') }}"
                title="@lang('auth.login')">
            @lang('auth.login')
        </a>
    @endif

    @if($hasPrevious)
        <a class="ui button {{ barauth()->isAuth() ? 'primary' : 'basic' }}"
                href="{{ url()->previous() }}"
                title="@lang('general.goBack')">
            @lang('general.goBack')
        </a>
    @endif

    <a class="ui button {{ barauth()->isAuth() && !$hasPrevious ? 'primary' : 'basic' }}"
            href="{{ route($homeRoute) }}"
            title="@lang('pages.' . $homeRoute)">
        @lang('pages.' . $homeRoute)
    </a>
@endsection
