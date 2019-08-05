@extends('layouts.app')

@section('title', __('pages.changePassword'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.changePasswordDescription')</p>

    {!! Form::open(['action' => ['PasswordChangeController@doChange'], 'method' => 'POST', 'class' => 'ui form']) !!}

        @if($has_password)
            <div class="field {{ ErrorRenderer::hasError('password') ? 'error' : '' }}">
                {{ Form::label('password', __('account.currentPassword') . ':') }}
                {{ Form::password('password') }}
                {{ ErrorRenderer::inline('password') }}
            </div>
        @endif

        <div class="two fields">
            <div class="field {{ ErrorRenderer::hasError('new_password') ? 'error' : '' }}">
                {{ Form::label('new_password', __('account.newPassword') . ':') }}
                {{ Form::password('new_password') }}
                {{ ErrorRenderer::inline('new_password') }}
            </div>

            <div class="field {{ ErrorRenderer::hasError('new_password_confirmation') ? 'error' : '' }}">
                {{ Form::label('new_password_confirmation', __('account.confirmNewPassword') . ':') }}
                {{ Form::password('new_password_confirmation') }}
                {{ ErrorRenderer::inline('new_password_confirmation') }}
            </div>
        </div>

        <div class="ui divider"></div>

        <div class="inline field">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('invalidate_other_sessions', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('invalidate_other_sessions', __('account.invalidateOtherSessions')) }}
            </div>
            {{ ErrorRenderer::inline('invalidate_other_sessions') }}
        </div>

        <br />

        <button class="ui button primary" type="submit">@lang('pages.changePassword')</button>
        <a href="{{ route('account', ['userId' => barauth()->getSessionUser()]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

        @if($can_disable)
            &nbsp;&nbsp;
            <a href="{{ route('password.disable') }}">
                @lang('pages.passwordDisable.title')
            </a>
        @endif

    {!! Form::close() !!}
@endsection
