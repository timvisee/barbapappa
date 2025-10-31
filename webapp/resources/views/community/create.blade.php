@extends('layouts.app')

@section('title', __('pages.community.createCommunity'))
@php
    $menusection = 'app_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['CommunityController@doCreate'], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="required field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', '', ['placeholder' => __('pages.community.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="field {{ ErrorRenderer::hasError('description') ? 'error' : '' }}">
            {{ Form::label('description', __('misc.description') . ' (' .  __('misc.public') . '):') }}
            {{ Form::textarea('description', '', ['placeholder' => __('pages.community.descriptionPlaceholder'), 'rows' => 3]) }}
            {{ ErrorRenderer::inline('description') }}
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
            {{ Form::label('slug', __('misc.slug') . ':') }}
            <div class="ui labeled input">
                {{ Form::label('slug', '/c/', ['class' => 'ui label basic']) }}
                {{ Form::text('slug', '', ['placeholder' => __('pages.community.slugPlaceholder')]) }}
            </div>
            {{ ErrorRenderer::inline('slug') }}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('misc.code')</div>
            <p>@lang('pages.community.codeDescription')</p>
        </div>

        <div class="field {{ ErrorRenderer::hasError('password') ? 'error' : '' }}">
            {{ Form::label('password', __('misc.code') . ':') }}
            {{ Form::text('password', '', ['placeholder' => __('misc.codePlaceholder')]) }}
            {{ ErrorRenderer::inline('password') }}
        </div>

        <div class="ui divider"></div>

        <div class="inline field {{ ErrorRenderer::hasError('show_explore') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('show_explore', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('show_explore', __('pages.community.showExploreDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('show_explore') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('self_enroll') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('self_enroll', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('self_enroll', __('pages.community.selfEnrollDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('self_enroll') }}
        </div>

        <div class="ui divider"></div>

        <div class="inline field">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('join', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('join', __('pages.community.joinAfterCreate')) }}
            </div>
            {{ ErrorRenderer::inline('join') }}
        </div>

        <br />

        <button class="ui button primary" type="submit">@lang('misc.create')</button>
        <a href="{{ url()->previous(route('explore.community')) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
