@extends('layouts.app')

@section('content')

    <h1>Verify email address</h1>

    {!! Form::open(['action' => ['EmailVerifyController@doVerify'], 'method' => 'POST']) !!}

        <p>Please enter the verification token of the email address you'd like to verify.</p>
        <p>This token can be found at the bottom of the verification email you've received in your mailbox.</p>

        {{ Form::label('token', 'Token') }}
        {{ Form::text('token', '') }}

        {{ Form::submit('Verify') }}

    {!! Form::close() !!}

@endsection
