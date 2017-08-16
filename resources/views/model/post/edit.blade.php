@extends('layouts.app')

@section('content')
    <h1>Edit post</h1>

    {!! Form::open(['action' => ['PostsController@update', $post->id], 'method' => 'POST']) !!}
        {{ Form::label('title', 'Title') }}
        {{ Form::text('title', $post->title, ['placeholder' => 'Title']) }}

        {{ Form::label('body', 'Body') }}
        {{ Form::textarea('body', $post->body, ['placeholder' => 'Body']) }}

        {{ Form::hidden('_method', 'PUT') }}

        {{ Form::submit('Update') }}
    {!! Form::close() !!}
@endsection