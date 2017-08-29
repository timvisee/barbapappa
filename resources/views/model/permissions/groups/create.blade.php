@extends('layouts.app')

@section('content')
    <h1>Edit permission group</h1>

    {!! Form::open(['action' => ['PermissionGroupsController@store'], 'method' => 'POST']) !!}

    <div class="ui-field-contain">
        {{ Form::label('name', 'Name:') }}
        {{ Form::text('name', '', ['placeholder' => 'My permission group']) }}
        {{ ErrorRenderer::inline('name') }}
    </div>

    <div class="ui-field-contain">
        {{ Form::label('enabled', 'Enabled') }}
        {{ Form::checkbox('enabled', 'true', true) }}
        {{ ErrorRenderer::inline('enabled') }}
    </div>

    TODO: Show inherit from<br />
    TODO: Show a box to select the community it's part of<br />
    TODO: Show a box to select the bar it's part of<br />

    {{ Form::submit('Create') }}

    {!! Form::close() !!}

@endsection