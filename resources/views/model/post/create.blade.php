@extends('layouts.app')

@section('content')
    <h1>Create post</h1>

    {!! Form::open(['action' => 'PostsController@store', 'method' => 'POST']) !!}
        {{ Form::label('title', 'Title') }}
        {{ Form::text('title', '', ['placeholder' => 'Title']) }}

        {{ Form::label('body', 'Body') }}
        {{ Form::textarea('body', '', ['placeholder' => 'Body']) }}

        {{ Form::submit('Create') }}
    {!! Form::close() !!}
@endsection