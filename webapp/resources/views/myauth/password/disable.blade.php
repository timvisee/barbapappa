@extends('layouts.app')

@section('title', __('pages.passwordDisable.title'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.passwordDisable.description')</p>

    {!! Form::open(['action' => ['PasswordChangeController@doDisable'], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="required field {{ ErrorRenderer::hasError('password') ? 'error' : '' }}">
            {{ Form::label('password', __('account.currentPassword') . ':') }}
            {{ Form::password('password') }}
            {{ ErrorRenderer::inline('password') }}
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

        <button class="ui button primary" type="submit">@lang('pages.passwordDisable.title')</button>
        <a href="{{ route('password.change', ['userId' => barauth()->getSessionUser()]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
