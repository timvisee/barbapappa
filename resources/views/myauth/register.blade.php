@extends('layouts.app')

@section('content')

    <h1>Register</h1>

    {!! Form::open(['action' => ['RegisterController@doRegister'], 'method' => 'POST']) !!}

        {{ Form::label('email', 'Email') }}
        {{ Form::text('email', '', ['placeholder' => 'me@domain.com']) }}

        {{ Form::label('first_name', 'First name') }}
        {{ Form::text('first_name', '', ['placeholder' => 'First name']) }}

        {{ Form::label('last_name', 'Last name') }}
        {{ Form::text('last_name', '', ['placeholder' => 'Last name']) }}

        {{ Form::label('password', 'Password') }}
        {{ Form::password('password') }}

        {{ Form::label('password_confirmation', 'Confirm password') }}
        {{ Form::password('password_confirmation') }}

        {{ Form::submit('Register') }}

        <a href="{{ route('login') }}">
            Login
        </a>

    {!! Form::close() !!}

@endsection
