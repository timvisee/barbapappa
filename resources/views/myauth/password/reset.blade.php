@extends('layouts.app')

@section('content')

    <h1>Change password</h1>

    {!! Form::open(['action' => ['PasswordResetController@doReset'], 'method' => 'POST']) !!}

        {{ Form::label('token', 'Token') }}
        {{ Form::text('token', $token) }}

        {{ Form::label('password', 'Password') }}
        {{ Form::password('password') }}

        {{ Form::label('password_confirmation', 'Confirm password') }}
        {{ Form::password('password_confirmation') }}

        {{ Form::submit('Change password') }}

    {!! Form::close() !!}

@endsection
