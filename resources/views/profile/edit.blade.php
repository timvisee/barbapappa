@extends('layouts.app')

@section('content')
    <h1>Edit profile</h1>

    {!! Form::open(['action' => ['ProfileController@update', $user->id], 'method' => 'POST']) !!}

        {{ Form::label('first_name', 'First name') }}
        {{ Form::text('first_name', $user->first_name) }}
        <br />

        {{ Form::label('last_name', 'Last name') }}
        {{ Form::text('last_name', $user->last_name) }}
        <br />

        {{ Form::hidden('_method', 'PUT') }}

        {{ Form::submit('Edit') }}

    {!! Form::close() !!}
@endsection
