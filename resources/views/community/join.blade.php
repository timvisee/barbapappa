@extends('layouts.app')

@section('content')

    <h2 class="ui header">{{ $community->name }}</h2>

    <p>@lang('pages.community.joinQuestion')</p>

    <div class="ui divider"></div>

    <div class="ui warning message visible">
        <div class="header">@lang('misc.protected')</div>
        <p>@lang('pages.community.protectedByCode')</p>
    </div>

    {!! Form::open(['action' => ['CommunityController@doJoin', 'communityId' => $community->id], 'method' => 'POST', 'class' => 'ui form']) !!}

        {{-- TODO: only show if community is password protected --}}
        <div class="field {{ ErrorRenderer::hasError('code') ? 'error' : '' }}">
            {{ Form::label('code', __('misc.code') . ':') }}
            {{ Form::text('code', '', ['placeholder' => __('misc.codePlaceholder')]) }}
            {{ ErrorRenderer::inline('code') }}
        </div>

        <br>

        <div class="ui buttons">
            <button class="ui button positive" type="submit">@lang('pages.community.yesJoin')</button>
            <div class="or" data-text="@lang('general.or')"></div>
            <a href="{{ route('community.show', ['communityId' => $community->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
        </div>
    {!! Form::close() !!}

@endsection
