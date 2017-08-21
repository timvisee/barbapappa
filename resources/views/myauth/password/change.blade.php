@extends('layouts.app')

@section('content')

    <h1>@lang('pages.changePassword')</h1>
    <p>@lang('pages.changePasswordDescription')</p>

    {!! Form::open(['action' => ['PasswordChangeController@doChange'], 'method' => 'POST']) !!}

        {{ Form::label('password', __('account.currentPassword')) }}
        {{ Form::password('password') }}
        <br />

        {{ Form::label('new_password', __('account.newPassword')) }}
        {{ Form::password('new_password') }}
        <br />

        {{ Form::label('new_password_confirmation', __('account.confirmNewPassword')) }}
        {{ Form::password('new_password_confirmation') }}
        <br />
        <br />

        <p>@lang('account.invalidateOtherSessionsDescription')</p>

        {{ Form::label('invalidate_other_sessions', __('account.invalidateOtherSessions')) }}
        {{ Form::checkbox('invalidate_other_sessions', 'true', true) }}
        <br />

        {{ Form::submit(__('pages.changePassword')) }}

    {!! Form::close() !!}

@endsection
