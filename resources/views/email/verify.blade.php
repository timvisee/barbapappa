@extends('layouts.app')

@section('content')

    <h2 class="ui header">@lang('pages.verifyEmail.title')</h2>

    {!! Form::open(['action' => ['EmailVerifyController@doVerify'], 'method' => 'POST']) !!}

        <p>@lang('pages.verifyEmail.description')</p>

        {{ Form::label('token', 'Token') }}
        @if(empty($token))
            {{ Form::text('token', '') }}
        @else
            {{ Form::text('token', $token) }}
        @endif
        {{ ErrorRenderer::inline('token') }}

        {{ Form::submit(__('misc.verify')) }}

    {!! Form::close() !!}

@endsection
