@extends('layouts.app')

@section('content')

    <h1>@lang('pages.requestPasswordReset')</h1>

    {!! Form::open(['action' => ['PasswordForgetController@doRequest'], 'method' => 'POST']) !!}

        {{ Form::label('email', __('account.email')) }}
        {{ Form::text('email', '', ['placeholder' => __('account.emailPlaceholder')]) }}
        {{ ErrorRenderer::inline('email') }}

        {{ Form::submit(__('account.resetPassword')) }}

        <a href="{{ route('login') }}">@lang('auth.login')</a>

    {!! Form::close() !!}

@endsection
