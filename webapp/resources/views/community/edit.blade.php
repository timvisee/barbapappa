@extends('layouts.app')

@section('title', __('pages.community.editCommunity'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['CommunityController@doEdit', $community->human_id], 'method' => 'PUT', 'class' => 'ui form']) !!}

        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', $community->name, ['placeholder' => __('pages.community.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="field {{ ErrorRenderer::hasError('description') ? 'error' : '' }}">
            {{ Form::label('description', __('misc.description') . ' (' .  __('misc.public') . ', ' .  __('general.optional') . '):') }}
            {{ Form::textarea('description', $community->description, ['placeholder' => __('pages.community.descriptionPlaceholder'), 'rows' => 3]) }}
            {{ ErrorRenderer::inline('description') }}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('misc.slug')</div>
            <p>@lang('pages.community.slugDescription')</p>

            <p>
                @lang('pages.community.slugDescriptionExample')</br>
                <u><code>{{ URL::to('/c/' . $community->id) }}</code></u>
                <span class="glyphicons glyphicons-chevron-right"></span>
                <u><code>{{ URL::to('/c/' . ($community->slug ?? __('pages.community.slugPlaceholder'))) }}</code></u>.
            </p>
        </div>

        <div class="field {{ ErrorRenderer::hasError('slug') ? 'error' : '' }}">
            {{ Form::label('slug', __('misc.slug') . ' (' .  __('general.optional') . '):') }}
            <div class="ui labeled input">
                <label for="slug" class="ui label">/c/</label>
                {{ Form::text('slug', $community->slug, ['placeholder' => __('pages.community.slugPlaceholder')]) }}
            </div>
            {{ ErrorRenderer::inline('slug') }}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('misc.code')</div>
            <p>@lang('pages.community.codeDescription')</p>
        </div>

        <div class="field {{ ErrorRenderer::hasError('password') ? 'error' : '' }}">
            {{ Form::label('password', __('misc.code') . ' (' .  __('general.optional') . '):') }}
            {{ Form::text('password', $community->password, ['placeholder' => __('misc.codePlaceholder')]) }}
            {{ ErrorRenderer::inline('password') }}
        </div>

        <div class="ui divider"></div>

        <div class="inline field {{ ErrorRenderer::hasError('show_explore') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="show_explore"
                        tabindex="0"
                        class="hidden"
                        {{ $community->show_explore ? 'checked="checked"' : '' }}>
                {{ Form::label('show_explore', __('pages.community.showExploreDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('show_explore') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('self_enroll') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="self_enroll"
                        tabindex="0"
                        class="hidden"
                        {{ $community->self_enroll ? 'checked="checked"' : '' }}>
                {{ Form::label('self_enroll', __('pages.community.selfEnrollDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('self_enroll') }}
        </div>

        <br />

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
