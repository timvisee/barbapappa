@extends('layouts.app')

@section('title', __('pages.errors.' . View::getSection('code') . '.title'))

@php
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

    @if(barauth()->isAuth())
        <a href="{{ route('last') }}" class="ui button primary">
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
