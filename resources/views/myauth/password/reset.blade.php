@extends('layouts.app')

@section('content')

    <h1>Reset your password</h1>

    {!! Form::open(['action' => ['PasswordResetController@doReset'], 'method' => 'POST']) !!}

        <p>Please enter the password reset token. This token can be found in the email message you've received with password reset instructions.</p>
        <br />

        {{ Form::label('token', 'Token') }}
        @if(!empty($token))
            {{ Form::text('token', $token) }}
        @else
            {{ Form::text('token', '') }}
        @endif
        <br />

        <p>Please enter the new password you'd like to use from now on.</p>
        <br />

        {{ Form::label('password', 'Password') }}
        {{ Form::password('password') }}
        <br />

        {{ Form::label('password_confirmation', 'Confirm password') }}
        {{ Form::password('password_confirmation') }}
        <br />

        <p>
            Check the box below to log you out from your account on all devices.<br />
            This option should be checked if you assume your account has been hijacked by someone else.
        </p>
        <br />

        {{ Form::label('invalidate_sessions', 'Log out on all devices') }}
        {{ Form::checkbox('invalidate_sessions', 'true', true) }}
        <br />

        {{ Form::submit('Change password') }}

    {!! Form::close() !!}

@endsection
