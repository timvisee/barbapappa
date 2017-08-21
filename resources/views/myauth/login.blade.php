@extends('layouts.app')

@section('content')

    <h1>@lang('auth.login')</h1>

    {!! Form::open(['action' => ['LoginController@doLogin'], 'method' => 'POST']) !!}

        {{ Form::label('email', __('account.email')) }}
        {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}

        {{ Form::label('password', __('account.password')) }}
        {{ Form::password('password') }}

        @if ($errors->has('password'))
            <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif

        {{ Form::submit(__('auth.login')) }}

        <a href="{{ route('password.request') }}">@lang('auth.forgotPassword')</a>

        <a href="{{ route('register') }}">@lang('auth.register')</a>

    {!! Form::close() !!}

@endsection
