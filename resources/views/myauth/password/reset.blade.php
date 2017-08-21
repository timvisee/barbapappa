@extends('layouts.app')

@section('content')

    <h1>@lang('account.resetPassword')</h1>

    {!! Form::open(['action' => ['PasswordResetController@doReset'], 'method' => 'POST']) !!}

        <p>@lang('pages.passwordReset.enterResetToken')</p>
        <br />

        {{ Form::label('token', __('misc.token')) }}
        @if(!empty($token))
            {{ Form::text('token', $token) }}
        @else
            {{ Form::text('token', '') }}
        @endif
        <br />

        <p>@lang('pages.passwordReset.enterNewPassword')</p>
        <br />

        {{ Form::label('password', __('account.newPassword')) }}
        {{ Form::password('password') }}
        <br />

        {{ Form::label('password_confirmation', __('account.confirmNewPassword')) }}
        {{ Form::password('password_confirmation') }}
        <br />

        <p>
            @if(barauth()->isAuth())
                @lang('account.invalidateOtherSessionsDescription')
            @else
                @lang('account.invalidateAllSessionsDescription')
            @endif
        </p>
        <br />

        @if(barauth()->isAuth())
            {{ Form::label('invalidate_other_sessions', __('account.invalidateOtherSessions')) }}
        @else
            {{ Form::label('invalidate_other_sessions', __('account.invalidateAllSessions')) }}
        @endif
        {{ Form::checkbox('invalidate_other_sessions', 'true') }}
        <br />

        {{ Form::submit(__('pages.changePassword')) }}

    {!! Form::close() !!}

@endsection
