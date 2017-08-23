@extends('layouts.app')

@section('content')

    {!! Form::open(['action' => ['LoginController@doLogin'], 'method' => 'POST', 'data-ajax' => 'false']) !!}

    <div class="ui-field-contain">
        {{ Form::label('email', __('account.email') . ':') }}
        {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}
        {{ ErrorRenderer::inline('email') }}
    </div>

    <div class="ui-field-contain">
        {{ Form::label('password', __('account.password') . ':') }}
        {{ Form::password('password') }}
        {{ ErrorRenderer::inline('password') }}
    </div>

    <br />
    {{ Form::submit(__('auth.login')) }}

    <br />
    <div data-role="controlgroup">
        <a href="{{ route('password.request') }}" class="ui-btn ui-btn-corner-all">@lang('auth.forgotPassword')</a>
        <a href="{{ route('register') }}" class="ui-btn ui-btn-corner-all">@lang('auth.register')</a>
    </div>

    {!! Form::close() !!}

@endsection
