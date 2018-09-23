@extends('layouts.app')

@section('content')

    {!! Form::open(['action' => ['RegisterController@doRegister'], 'method' => 'POST', 'class' => 'ui form']) !!}

    <div class="field {{ ErrorRenderer::hasError('email') ? 'error' : '' }}">
        {{ Form::label('email', __('account.email') . ':') }}
        {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}
        {{ ErrorRenderer::inline('email') }}
    </div>

    <div class="two fields">
        <div class="field {{ ErrorRenderer::hasError('first_name') ? 'error' : '' }}">
            {{ Form::label('first_name', __('account.firstName') . ':') }}
            {{ Form::text('first_name', '', ['placeholder' => __('account.firstNamePlaceholder')]) }}
            {{ ErrorRenderer::inline('first_name') }}
        </div>

        <div class="field {{ ErrorRenderer::hasError('last_name') ? 'error' : '' }}">
            {{ Form::label('last_name', __('account.lastName') . ':') }}
            {{ Form::text('last_name', '', ['placeholder' => __('account.lastNamePlaceholder')]) }}
            {{ ErrorRenderer::inline('last_name') }}
        </div>
    </div>

    <div class="two fields">
        <div class="field {{ ErrorRenderer::hasError('password') ? 'error' : '' }}">
            {{ Form::label('password', __('account.password') . ':') }}
            {{ Form::password('password', '') }}
            {{ ErrorRenderer::inline('password') }}
        </div>

        <div class="field {{ ErrorRenderer::hasError('password_confirmation') ? 'error' : '' }}">
            {{ Form::label('password_confirmation', __('account.confirmPassword') . ':') }}
            {{ Form::password('password_confirmation', '') }}
            {{ ErrorRenderer::inline('password_confirmation') }}
        </div>
    </div>

    <div data-role="ui buttons">
        <button class="ui button primary" type="submit">@lang('auth.register')</button>
        <a href="{{ route('login') }}" class="ui button basic">@lang('auth.login')</a>
    </div>

    {!! Form::close() !!}

@endsection
