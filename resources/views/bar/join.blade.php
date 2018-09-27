@extends('layouts.app')

@section('content')

    <h2 class="ui header">{{ $bar->name }}</h2>

    <p>@lang('pages.bar.joinQuestion')</p>

    @php
        $user = barauth()->getSessionUser();
        $needsPassword = $bar->needsPassword($user);
        $code = Request::query('code');
    @endphp

    @if($needsPassword)
        <div class="ui divider"></div>

        @if(empty($code))
            <div class="ui warning message visible">
                <div class="header">@lang('misc.protected')</div>
                <p>@lang('pages.bar.protectedByCode')</p>
            </div>
        @else
            <div class="ui info message visible">
                <div class="header">@lang('misc.protected')</div>
                <p>@lang('pages.bar.protectedByCodeFilled')</p>
            </div>
        @endif
    @endif

    {!! Form::open(['action' => ['BarController@doJoin', 'barId' => $bar->id], 'method' => 'POST', 'class' => 'ui form']) !!}

        @if($needsPassword)
            <div class="field {{ ErrorRenderer::hasError('code') ? 'error' : '' }}">
                {{ Form::label('code', __('misc.code') . ':') }}
                {{ Form::text('code', $code, ['placeholder' => __('misc.codePlaceholder')]) }}
                {{ ErrorRenderer::inline('code') }}
            </div>
        @endif

        @if(!$community->isJoined(barauth()->getSessionUser()))
            <br>
            <div class="inline field">
                <div class="ui toggle checkbox">
                    <input type="checkbox" name="join_community" tabindex="0" class="hidden" checked="checked">
                    {{ Form::label('join_community', __('pages.bar.alsoJoinCommunity') . ': ' . $community->name) }}
                </div>
                {{ ErrorRenderer::inline('join_community') }}
            </div>
        @else
            <p>
                @lang('pages.bar.alreadyJoinedTheirCommunity'):
                {{ $community->name }}
            </p>
        @endif

        <br />

        <div class="ui buttons">
            <button class="ui button positive" type="submit">@lang('pages.bar.yesJoin')</button>
            <div class="or" data-text="@lang('general.or')"></div>
            <a href="{{ route('bar.show', ['barId' => $bar->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
        </div>
    {!! Form::close() !!}

@endsection
