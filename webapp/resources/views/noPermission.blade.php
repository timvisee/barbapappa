{{-- prevent leaking personal info with no permission using dontLeak --}}
@extends('layouts.app', ['dontLeak' => true])

@section('title', __('pages.noPermission.title'))

@php
    // Get the home route name to use based on the session
    $homeRoute = barauth()->isAuth() ? 'dashboard' : 'index';
    $homeRouteName = __('pages.' . (barauth()->isAuth() ? 'dashboard.title' : 'index'));

    // Determine whether we have a previous URL
    $hasPrevious = url()->previous() != url()->current();

    // Define menulinks
    if($hasPrevious)
        $menulinks[] = [
            'name' => __('general.goBack'),
            'link' => url()->previous(),
            'icon' => 'undo',
        ];
    $menulinks[] = [
        'name' => $homeRouteName,
        'link' => route($homeRoute),
        'icon' => 'text-underline',
    ];
@endphp

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

    @if(barauth()->isAuth())
        <a href="{{ route('last') }}" class="ui button primary">
            @lang('pages.last.title')
        </a>
    @else
        {{-- TODO: redirect to this page after login --}}
        <a class="ui button primary"
                href="{{ route('login') }}"
                title="@lang('auth.login')">
            @lang('auth.login')
        </a>
    @endif


    @if($hasPrevious)
        <a class="ui button basic"
                href="{{ url()->previous() }}"
                title="@lang('general.goBack')">
            @lang('general.goBack')
        </a>
    @endif

    <a class="ui button basic"
            href="{{ route($homeRoute) }}"
            title="{{ $homeRouteName }}">
        {{ $homeRouteName }}
    </a>
@endsection
