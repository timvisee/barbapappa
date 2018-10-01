@extends('layouts.app')

@section('content')
    <h1>Edit permission group</h1>

    {!! Form::open(['action' => ['PermissionGroupsController@update', $group->id], 'method' => 'PUT']) !!}

    <div class="ui-field-contain">
        {{ Form::label('name', 'Name') }}
        {{ Form::text('name', $group->name) }}
        {{ ErrorRenderer::inline('name') }}
    </div>

    <div class="ui-field-contain">
        {{ Form::label('enabled', 'Enabled') }}
        {{ Form::checkbox('enabled', 'true', $group->enabled) }}
        {{ ErrorRenderer::inline('enabled') }}
    </div>

    TODO: Show inherit from<br />

    {{ Form::submit('Update') }}

    {!! Form::close() !!}

@endsection
