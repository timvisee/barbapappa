@extends('layouts.app')

@section('content')
    <h2 class="ui header">@lang('pages.community.createCommunity')</h2>

    {!! Form::open(['action' => ['CommunityController@doCreate'], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', '', ['placeholder' => __('pages.community.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('misc.slug')</div>
            <p>@lang('pages.community.slugDescription')</p>

            <p>
                @lang('pages.community.slugDescriptionExample')</br>
                <u><code>{{ URL::to('/c/123') }}</code></u>
                <span class="glyphicons glyphicons-chevron-right"></span>
                <u><code>{{ URL::to('/c/' . __('pages.community.slugPlaceholder')) }}</code></u>.
            </p>
        </div>

        <div class="field {{ ErrorRenderer::hasError('slug') ? 'error' : '' }}">
            {{ Form::label('slug', __('misc.slug') . ' (' .  __('general.optional') . '):') }}
            {{ Form::text('slug', '', ['placeholder' => __('pages.community.slugPlaceholder')]) }}
            {{ ErrorRenderer::inline('slug') }}
            {{-- TODO: suggest a clickable slug based on the community name --}}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('misc.code')</div>
            <p>@lang('pages.community.codeDescription')</p>
        </div>

        <div class="field {{ ErrorRenderer::hasError('password') ? 'error' : '' }}">
            {{ Form::label('password', __('misc.code') . ' (' .  __('general.optional') . '):') }}
            {{ Form::text('password', '', ['placeholder' => __('misc.codePlaceholder')]) }}
            {{ ErrorRenderer::inline('password') }}
        </div>

        <div class="ui divider"></div>

        <div class="inline field {{ ErrorRenderer::hasError('visible') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="visible"
                        tabindex="0"
                        class="hidden"
                        checked="checked">
                {{ Form::label('visible', __('pages.community.visibleDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('visible') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('public') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="public"
                        tabindex="0"
                        class="hidden">
                {{ Form::label('public', __('pages.community.publicDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('public') }}
        </div>

        <br />

        <button class="ui button primary" type="submit">@lang('misc.create')</button>
        <a href="{{ url()->previous() }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
