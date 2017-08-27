@extends('layouts.app')

@section('content')

    <div class="highlight-box">
        <h3>@lang('misc.welcomeTo')</h3>
        <img src="{{ asset('img/logo/logo_wrap.svg') }}" class="logo" />
    </div>

    <br />
    <ul class="ui-listview center" data-role="listview">
        <li>
            <a href="{{ route('login') }}" class="ui-btn ui-btn-icon-left ui-icon-glyphicons ui-icon-glyphicons-user">
                @lang('auth.login')
            </a>
        </li>
        <li>
            <a href="{{ route('register') }}" class="ui-btn ui-btn-icon-left ui-icon-glyphicons ui-icon-glyphicons-user-asterisk">
                @lang('auth.register')
            </a>
        </li>
    </ul>

@endsection
