@extends('layouts.app')

@section('content')

    {!! Form::open(['action' => ['RegisterController@doRegister'], 'method' => 'POST', 'data-ajax' => 'false']) !!}

    <div class="ui-field-contain">
        {{ Form::label('email', __('account.email') . ':') }}
        {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}
        {{ ErrorRenderer::inline('email') }}
    </div>

    <div class="ui-field-contain">
        {{ Form::label('first_name', __('account.firstName') . ':') }}
        {{ Form::text('first_name', '', ['placeholder' => __('account.firstNamePlaceholder')]) }}
        {{ ErrorRenderer::inline('first_name') }}
    </div>

    <div class="ui-field-contain">
        {{ Form::label('last_name', __('account.lastName') . ':') }}
        {{ Form::text('last_name', '', ['placeholder' => __('account.lastNamePlaceholder')]) }}
        {{ ErrorRenderer::inline('last_name') }}
    </div>

    <div class="ui-field-contain">
        {{ Form::label('password', __('account.password') . ':') }}
        {{ Form::password('password') }}
        {{ ErrorRenderer::inline('password') }}
    </div>

    <div class="ui-field-contain">
        {{ Form::label('password_confirmation', __('account.confirmPassword') . ':') }}
        {{ Form::password('password_confirmation') }}
        {{ ErrorRenderer::inline('password_confirmation') }}
    </div>

    <br />
    {{ Form::submit(__('auth.register')) }}

    <br />
    <a href="{{ route('login') }}" class="ui-btn ui-btn-corner-all">@lang('auth.login')</a>

    {!! Form::close() !!}

@endsection
