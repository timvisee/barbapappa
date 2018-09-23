@extends('layouts.app')

@section('content')

    <h2 class="ui header">@lang('pages.changePassword')</h2>
    <p>@lang('pages.changePasswordDescription')</p>

    {!! Form::open(['action' => ['PasswordChangeController@doChange'], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="field {{ ErrorRenderer::hasError('password') ? 'error' : '' }}">
            {{ Form::label('password', __('account.currentPassword') . ':') }}
            {{ Form::password('password') }}
            {{ ErrorRenderer::inline('password') }}
        </div>

        <div class="two fields">
            <div class="field {{ ErrorRenderer::hasError('new_password') ? 'error' : '' }}">
                {{ Form::label('new_password', __('account.newPassword') . ':') }}
                {{ Form::password('new_password', '') }}
                {{ ErrorRenderer::inline('new_password') }}
            </div>

            <div class="field {{ ErrorRenderer::hasError('new_password_confirmation') ? 'error' : '' }}">
                {{ Form::label('new_password_confirmation', __('account.confirmNewPassword') . ':') }}
                {{ Form::password('new_password_confirmation', '') }}
                {{ ErrorRenderer::inline('new_password_confirmation') }}
            </div>
        </div>

        <div class="ui divider"></div>

        <div class="ui warning message visible">
            <div class="header">@lang('account.isCompromised')</div>
            <p>@lang('account.invalidateOtherSessionsDescription')</p>
        </div>

        <div class="inline field">
            <div class="ui toggle checkbox">
                <input type="checkbox" name="invalidate_other_sessions" tabindex="0" class="hidden">
                {{ Form::label('invalidate_other_sessions', __('account.invalidateOtherSessions')) }}
            </div>
            {{ ErrorRenderer::inline('invalidate_other_sessions') }}
        </div>

        <br />

        <button class="ui button primary" type="submit">@lang('pages.changePassword')</button>

    {!! Form::close() !!}

@endsection
