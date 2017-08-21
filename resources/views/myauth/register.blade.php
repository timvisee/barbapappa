@extends('layouts.app')

@section('content')

    <h1>@lang('auth.register')</h1>

    {!! Form::open(['action' => ['RegisterController@doRegister'], 'method' => 'POST']) !!}

        {{ Form::label('email', __('account.email')) }}
        {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}

        {{ Form::label('first_name', __('account.firstName')) }}
        {{ Form::text('first_name', '', ['placeholder' => __('account.firstNamePlaceholder')]) }}

        {{ Form::label('last_name', __('account.lastName')) }}
        {{ Form::text('last_name', '', ['placeholder' => __('account.lastNamePlaceholder')]) }}

        {{ Form::label('password', __('account.password')) }}
        {{ Form::password('password') }}

        {{ Form::label('password_confirmation', __('account.confirmPassword')) }}
        {{ Form::password('password_confirmation') }}

        {{ Form::submit(__('auth.register')) }}

        <a href="{{ route('login') }}">@lang('auth.login')</a>

    {!! Form::close() !!}

@endsection
