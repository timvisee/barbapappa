@extends('layouts.app')

@section('content')

    <h1>Request password reset</h1>

    {!! Form::open(['action' => ['PasswordController@doRequest'], 'method' => 'POST']) !!}

        {{ Form::label('email', 'Email') }}
        {{ Form::text('email', '', ['placeholder' => 'me@domain.com']) }}

        {{ Form::submit('Reset password') }}

        <a href="{{ route('login') }}">
            Login
        </a>

    {!! Form::close() !!}

@endsection
