@extends('layouts.app')

@section('content')

    <h1>Reset password</h1>

    {!! Form::open(['action' => ['PasswordController@doRequest'], 'method' => 'POST']) !!}

        {{ Form::label('token', 'Token') }}
        {{ Form::text('token', $token) }}

        {{ Form::label('password', 'Password') }}
        {{ Form::password('password') }}

        {{ Form::label('password_verify', 'Password verification') }}
        {{ Form::password('password_verify') }}

        {{ Form::submit('Reset password') }}

    {!! Form::close() !!}

@endsection
