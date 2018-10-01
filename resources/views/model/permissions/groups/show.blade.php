@extends('layouts.app')

@section('content')
    <h1>Permission group</h1>
    <table>
        <tr>
            <td>Name:</td>
            <td>{{ $group->name }}</td>
        </tr>
        <tr>
            <td>Enabled:</td>
            <td>{{ $group->enabled ? 'Yes' : 'No'}}</td>
        </tr>
        <tr>
            <td>Inherit from:</td>
            <td>
                @if($group->inherit != null)
                    <a href="{{ route('permissionGroups.show', ['id' => $group->inherit->id]) }}">{{ $group->inherit->name }}</a>
                @else
                    <i>None</i>
                @endif
            </td>
        </tr>
        <tr>
            <td>Layer:</td>
            <td>
                @if($group->isApplicationLayer())
                    Application
                @elseif($group->isCommunityLayer())
                    Community
                @else
                    Bar
                @endif
            </td>
        </tr>
        @if($group->isCommunityLayer())
            <tr>
                <td>Community:</td>
                <td><a href="#">{{ $group->community }}</a></td>
            </tr>
        @endif
        @if($group->isBarLayer())
            <tr>
                <td>Bar:</td>
                <td><a href="#">{{ $group->bar }}</a></td>
            </tr>
        @endif
        <tr>
            <td>Created at:</td>
            <td>{{ $group->created_at }}</td>
        </tr>
        <tr>
            <td>Updated at:</td>
            <td>{{ $group->updated_at }}</td>
        </tr>
    </table>

    @if($group->inherited_by->count() > 0)
        <ul class="ui-listview" data-role="listview" data-inset="true">
            <li data-role="list-divider">Inherited by</li>
            @foreach($group->inherited_by as $other)
                <li><a href="{{ route('permissionGroups.show', ['id' => $other->id]) }}">{{ $other->name }}</a></li>
            @endforeach
        </ul>
        <br />
    @endif

    <hr />

    {{-- The user must have permission --}}
    <a href="/permissions/groups/{{ $group->id }}/edit" class="ui-btn ui-btn-corner-all">Edit</a>
    {!! Form::open(['action' => ['PermissionGroupsController@destroy', $group->id], 'method' => 'DELETE']) !!}
        {!! Form::submit('Delete') !!}
    {!! Form::close() !!}

@endsection
