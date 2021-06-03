@extends('layouts.app')

@section('title', __('auth.login'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['LoginController@doLogin'], 'method' => 'POST', 'class' => 'ui form']) !!}

    <div class="required field {{ ErrorRenderer::hasError('email') ? 'error' : '' }}">
        {{ Form::label('email', __('account.email') . ':') }}
        {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}
        {{ ErrorRenderer::inline('email') }}
    </div>

    <div class="required field {{ ErrorRenderer::hasError('password') ? 'error' : '' }}">
        {{ Form::label('password', __('account.password') . ':') }}
        {{ Form::password('password') }}
        {{ ErrorRenderer::inline('password') }}
    </div>

    <div>
        <button class="ui button primary" type="submit">@lang('auth.login')</button>
        @if(config('app.auth_session_link'))
            <a href="{{ route('login.email') }}" class="ui button basic">@lang('auth.loginEmail')</a>
        @endif

        <div class="ui link list">
            <a href="{{ route('password.request') }}" class="item">@lang('auth.forgotPassword')</a>
            <a href="{{ route('register') }}" class="item">@lang('auth.register')</a>
        </div>
    </div>

    {!! Form::close() !!}
@endsection
