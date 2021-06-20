@extends('layouts.app')

@section('title', __('pages.errors.' . View::getSection('code') . '.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('error');
@endphp

@php
    // Get some properties
    $hintLogin = !!View::getSection('hintLogin', false) && !barauth()->isAuth();

    // Get the home route name to use based on the session
    $homeRoute = barauth()->isAuth() ? 'dashboard' : 'index';
    $homeRouteName = __('pages.' . (barauth()->isAuth() ? 'dashboard' : 'index') . '.title');

    // Determine whether we have a previous URL
    $hasPrevious = url()->previous() != url()->current();
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.errors.' . View::getSection('code') . '.description')</p>

    <br />

    @if($hintLogin)
        <a class="ui button primary" href="{{ route('login') }}"
                title="@lang('auth.login')">
            @lang('auth.login')
        </a>
    @endif

    @if(barauth()->isAuth())
        <a href="{{ route('last') }}" class="ui button {{ $hintLogin ? 'basic' : 'primary'}}">
            @lang('pages.last.title')
        </a>
    @endif

    @if($hasPrevious)
        <a class="ui button basic"
                href="{{ url()->previous() }}"
                title="@lang('general.goBack')">
            @lang('general.goBack')
        </a>
    @endif

    <a class="ui button {{ !barauth()->isAuth() && !$hasPrevious ? 'primary' : 'basic' }}"
            href="{{ route($homeRoute) }}"
            title="{{ $homeRouteName }}">
        {{ $homeRouteName }}
    </a>
@endsection
