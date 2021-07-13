@extends('layouts.app')

@section('title', __('account.expireAllSessions'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('account.expireAllQuestion')</p>

    <div class="ui hidden divider"></div>

    {!! Form::open(['action' => ['SessionController@doExpireAll', 'userId' => $user->id], 'method' => 'DELETE', 'class' => 'ui form']) !!}
        <div class="inline field">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('expire_current', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('expire_current', __('account.expireCurrentSession')) }}
            </div>
            {{ ErrorRenderer::inline('expire_current') }}
        </div>

        <div class="inline field">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('expire_same_network', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('expire_same_network', __('account.expireSameNetworkSessions')) }}
            </div>
            {{ ErrorRenderer::inline('expire_same_network') }}
        </div>

        <div class="inline disabled field">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('expire_other', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('expire_other', __('account.invalidateOtherSessions')) }}
            </div>
            {{ ErrorRenderer::inline('expire_other') }}
        </div>

        <div class="ui hidden divider"></div>

        <div class="ui buttons">
            <a href="{{ route('account.sessions', ['userId' => $user->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesTerminate')</button>
        </div>
    {!! Form::close() !!}
@endsection

