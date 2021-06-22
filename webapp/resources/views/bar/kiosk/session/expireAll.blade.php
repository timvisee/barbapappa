@extends('layouts.app')

@section('title', __('account.expireAllSessions'))
@php
    $menusection = 'bar_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.bar.expireAllKioskSessionsQuestion')</p>

    <div class="ui hidden divider"></div>

    {!! Form::open(['action' => ['KioskSessionController@doExpireAll', 'barId' => $bar->human_id], 'method' => 'DELETE', 'class' => 'ui form']) !!}
        <div class="ui buttons">
            <a href="{{ route('bar.kiosk.sessions', ['barId' => $bar->human_id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesExpire')</button>
        </div>
    {!! Form::close() !!}
@endsection

