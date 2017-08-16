@extends('layouts.app')

@section('content')

    <h1>Login</h1>

    {!! Form::open(['action' => ['LoginController@doLogin'], 'method' => 'POST']) !!}

        {{ Form::label('email', 'Email') }}
        {{ Form::text('email', '', ['placeholder' => 'me@domain.com']) }}

        {{ Form::label('password', 'Password') }}
        {{ Form::password('password') }}

        @if ($errors->has('password'))
            <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif

        {{ Form::submit('Login') }}

        <a href="{{ route('password.request') }}">
            Forgot Your Password?
        </a>

        <a href="{{ route('register') }}">
            Register
        </a>

    {!! Form::close() !!}

@endsection
