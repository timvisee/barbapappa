@extends('layouts.app')

@section('content')

    <h1>Password reset!</h1>
    <p>
        Your password has successfully been reset.<br>
        From now on, use your new password to login to your account.
    </p>

    <a href="{{ route('dashboard') }}">Dashboard</a><br />
    <a href="{{ route('account') }}">My account</a>

@endsection
