@extends('layouts.app')

@section('content')

    {!! Form::open(['action' => ['LoginController@doLogin'], 'method' => 'POST', 'class' => 'ui form']) !!}

    <div class="field {{ ErrorRenderer::hasError('email') ? 'error' : '' }}">
        {{ Form::label('email', __('account.email') . ':') }}
        {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}
        {{ ErrorRenderer::inline('email') }}
    </div>

    <div class="field {{ ErrorRenderer::hasError('password') ? 'error' : '' }}">
        {{ Form::label('password', __('account.password') . ':') }}
        {{ Form::password('password') }}
        {{ ErrorRenderer::inline('password') }}
    </div>

    <div data-role="ui buttons">
        <button class="ui button primary" type="submit">@lang('auth.login')</button>
        <a href="{{ route('password.request') }}" class="ui button basic">@lang('auth.forgotPassword')</a>
        <a href="{{ route('register') }}" class="ui button basic">@lang('auth.register')</a>
    </div>

    {!! Form::close() !!}

@endsection
