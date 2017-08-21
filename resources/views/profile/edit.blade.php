@extends('layouts.app')

@section('content')
    <h1>@lang('pages.editProfile')</h1>

    {!! Form::open(['action' => ['ProfileController@update', $user->id], 'method' => 'POST']) !!}

        {{ Form::label('first_name', __('account.firstName')) }}
        {{ Form::text('first_name', $user->first_name) }}
        <br />

        {{ Form::label('last_name', __('account.lastName')) }}
        {{ Form::text('last_name', $user->last_name) }}
        <br />

        {{ Form::hidden('_method', 'PUT') }}

        {{ Form::submit(__('misc.saveChanges')) }}

    {!! Form::close() !!}
@endsection
