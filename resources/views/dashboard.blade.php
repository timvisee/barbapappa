@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    You are logged in!
                </div>

                @if(count($posts) > 0)
                    <h3>Your posts</h3>
                    @foreach($posts as $post)
                        <h4>{{ $post->title }}</h4>
                        <p>{{ $post->body }}</p>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
