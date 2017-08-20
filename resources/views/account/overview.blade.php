@extends('layouts.app')

@section('content')
    <h1>Your account</h1>
    <p>This page shows an overview of your account.</p>

    <h3>Profile</h3>
    <table>
        <tr>
            <td>First name:</td>
            <td>{{ $user->first_name }}</td>
        </tr>
        <tr>
            <td>Last name:</td>
            <td>{{ $user->last_name }}</td>
        </tr>
    </table>
    <a href="{{ route('profile.edit', ['userId' => $user->id]) }}">Edit profile</a><br />
    <a href="{{ route('password.change') }}">Change password</a>

    <h3>Email</h3>
    <ul>
        <li>timvisee@gmail.com</li>
    </ul>
@endsection
