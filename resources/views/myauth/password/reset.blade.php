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

        {{ Form::label('password', 'New password') }}
        {{ Form::password('password') }}
        <br />

        {{ Form::label('password_confirmation', 'Confirm new password') }}
        {{ Form::password('password_confirmation') }}
        <br />

        <p>
            @if(barauth()->isAuth())
                Check the box below to log out on all other devices.<br />
            @else
                Check the box below to log out on all devices.<br />
            @endif
            This option should be checked if you believe your account may have been used by someone else.
        </p>
        <br />

        @if(barauth()->isAuth())
            {{ Form::label('invalidate_other_sessions', 'Log out on other devices') }}
        @else
            {{ Form::label('invalidate_other_sessions', 'Log out on all devices') }}
        @endif
        <br />

        {{ Form::submit('Change password') }}

    {!! Form::close() !!}

@endsection
