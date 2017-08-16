@extends('layouts.app')

@section('content')
    <a href="/posts" class="btn btn-default">Go back</a>

    <h1>{{ $post->title }}</h1>
    <p>
        {{ $post->body }}
    </p>

    <hr />
    <small>Written on {{ $post->created_at }}</small>

    <hr />

    @if(!Auth::guest() && Auth::user()->id == $post->user_id)
        <a href="/posts/{{ $post->id }}/edit">Edit</a>
        {!! Form::open(['action' => ['PostsController@destroy', $post->id], 'method' => 'POST']) !!}
            {!! Form::hidden('_method', 'DELETE') !!}
            {!! Form::submit('Delete') !!}
        {!! Form::close() !!}
    @endif
@endsection