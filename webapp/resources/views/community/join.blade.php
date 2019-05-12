@extends('layouts.app')

@section('title', $community->name)

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.community.joinQuestion')</p>

    @php
        $user = barauth()->getSessionUser();
        $needsPassword = $community->needsPassword($user);
        $code = Request::query('code');
    @endphp

    @if($needsPassword)
        <div class="ui divider"></div>

        @if(empty($code))
            <div class="ui warning message visible">
                <div class="header">@lang('misc.protected')</div>
                <p>@lang('pages.community.protectedByCode')</p>
            </div>
        @else
            <div class="ui info message visible">
                <div class="header">@lang('misc.protected')</div>
                <p>@lang('pages.community.protectedByCodeFilled')</p>
            </div>
        @endif
    @endif

    {!! Form::open(['action' => ['CommunityController@doJoin', 'communityId' => $community->human_id], 'method' => 'POST', 'class' => 'ui form']) !!}

        @if($needsPassword)
            <div class="field {{ ErrorRenderer::hasError('code') ? 'error' : '' }}">
                {{ Form::label('code', __('misc.code') . ':') }}
                {{ Form::text('code', $code, ['placeholder' => __('misc.codePlaceholder')]) }}
                {{ ErrorRenderer::inline('code') }}
            </div>
            <br>
        @endif

        <div class="ui buttons">
            <button class="ui button positive" type="submit">@lang('pages.community.yesJoin')</button>
            <div class="or" data-text="@lang('general.or')"></div>
            <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
        </div>
    {!! Form::close() !!}
@endsection
