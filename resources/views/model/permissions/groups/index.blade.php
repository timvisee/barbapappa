@extends('layouts.app')

@section('content')
    <h1>Permission groups</h1>

    @if($groups->count() > 0)
        {{$groups->links()}}

        <ul class="ui-listview" data-role="listview" data-inset="true">
        @foreach($groups as $group)
            <li><a href="{{ route('permissionGroups.show', ['id' => $group->id]) }}">{{ $group->name }}</a></li>
        @endforeach
        </ul>

        {{$groups->links()}}
    @else
        <p>No posts found</p>
    @endif

    <a href="{{ route('permissionGroups.create') }}">Create</a>

@endsection