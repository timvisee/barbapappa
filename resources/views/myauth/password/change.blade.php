@extends('layouts.app')

@section('content')

    <h1>Change password</h1>
    <p>
        To change your password, fill in the fields below.
    </p>

    {!! Form::open(['action' => ['PasswordChangeController@doChange'], 'method' => 'POST']) !!}

        {{ Form::label('password', 'Current password') }}
        {{ Form::password('password') }}
        <br />

        {{ Form::label('new_password', 'New password') }}
        {{ Form::password('new_password') }}
        <br />

        {{ Form::label('new_password_confirmation', 'Confirm new password') }}
        {{ Form::password('new_password_confirmation') }}
        <br />
        <br />

        <p>
            Check the box below to log you out from your account on all other devices.<br />
            This option should be checked if you believe your account may have been used by someone else.
        </p>

        {{ Form::label('invalidate_other_sessions', 'Log out on other devices') }}
        {{ Form::checkbox('invalidate_other_sessions', 'true', true) }}
        <br />

        {{ Form::submit('Change password') }}

    {!! Form::close() !!}

@endsection
