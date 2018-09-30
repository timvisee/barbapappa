@extends('layouts.app')

@section('content')
    <h2 class="ui header">
        {{ $community->name }}
    </h2>

    {!! Form::open(['action' => ['CommunityController@update', $community->id], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', $community->name, ['placeholder' => __('pages.community.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="field {{ ErrorRenderer::hasError('slug') ? 'error' : '' }}">
            {{ Form::label('slug', __('misc.slug') . ':') }}
            {{ Form::text('slug', $community->slug, ['placeholder' => __('pages.community.slugPlaceholder')]) }}
            {{ ErrorRenderer::inline('slug') }}
            {{-- TODO: suggest a clickable slug based on the community name --}}
        </div>

        <div class="field {{ ErrorRenderer::hasError('password') ? 'error' : '' }}">
            {{ Form::label('password', __('misc.code') . ':') }}
            {{ Form::text('password', $community->password, ['placeholder' => __('misc.codePlaceholder')]) }}
            {{ ErrorRenderer::inline('password') }}
        </div>

        <div class="inline field">
            <div class="ui toggle checkbox">
                <input type="checkbox"
                        name="visible"
                        tabindex="0"
                        class="hidden"
                        {{ $community->visible ? 'checked="checked"' : '' }}>
                {{ Form::label('visible', __('misc.visible')) }}
            </div>
            {{ ErrorRenderer::inline('visible') }}
        </div>

        <div class="inline field">
            <div class="ui toggle checkbox">
                <input type="checkbox"
                        name="public"
                        tabindex="0"
                        class="hidden"
                        {{ $community->public ? 'checked="checked"' : '' }}>
                {{ Form::label('public', __('misc.public')) }}
            </div>
            {{ ErrorRenderer::inline('public') }}
        </div>

        {{ Form::hidden('_method', 'PUT') }}

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>

    {!! Form::close() !!}
@endsection
