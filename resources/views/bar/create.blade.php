@extends('layouts.app')

@section('content')
    <h2 class="ui header">@lang('pages.bar.createBar')</h2>

    {!! Form::open(['action' => ['BarController@doCreate', 'communityId' => $community->human_id], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', '', ['placeholder' => __('pages.bar.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('misc.slug')</div>
            <p>@lang('pages.bar.slugDescription')</p>

            <p>
                @lang('pages.bar.slugDescriptionExample')</br>
                <u><code>{{ URL::to('/b/123') }}</code></u>
                <span class="glyphicons glyphicons-chevron-right"></span>
                <u><code>{{ URL::to('/b/' . __('pages.bar.slugPlaceholder')) }}</code></u>.
            </p>
        </div>

        <div class="field {{ ErrorRenderer::hasError('slug') ? 'error' : '' }}">
            {{ Form::label('slug', __('misc.slug') . ' (' .  __('general.optional') . '):') }}
            {{ Form::text('slug', '', ['placeholder' => __('pages.bar.slugPlaceholder')]) }}
            {{ ErrorRenderer::inline('slug') }}
            {{-- TODO: suggest a clickable slug based on the bar name --}}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('misc.code')</div>
            <p>@lang('pages.bar.codeDescription')</p>
        </div>

        <div class="field {{ ErrorRenderer::hasError('password') ? 'error' : '' }}">
            {{ Form::label('password', __('misc.code') . ' (' .  __('general.optional') . '):') }}
            {{ Form::text('password', '', ['placeholder' => __('misc.codePlaceholder')]) }}
            {{ ErrorRenderer::inline('password') }}
        </div>

        <div class="ui divider"></div>

        <div class="field {{ ErrorRenderer::hasError('economy') ? 'error' : '' }}">
            {{ Form::label('economy', __('pages.community.economy')) }}

            <div class="ui fluid selection dropdown">
                <input type="hidden" name="economy">
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.unspecified')</div>
                <div class="menu">
                    @foreach($community->economies()->get() as $economy)
                        <div class="item" data-value="{{ $economy->id }}">{{ $economy->name }}</div>
                    @endforeach
                </div>
            </div>

            {{ ErrorRenderer::inline('economy') }}
        </div>

        <a href="{{ route('community.economy.index', ['communityId' => $community->human_id]) }}">
            @lang('pages.economies.manage')
        </a>

        <div class="ui divider"></div>

        <div class="inline field {{ ErrorRenderer::hasError('visible') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="visible"
                        tabindex="0"
                        class="hidden"
                        checked="checked">
                {{ Form::label('visible', __('pages.bar.visibleDescription')) }}
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
                {{ Form::label('public', __('pages.bar.publicDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('public') }}
        </div>

        <br />

        <button class="ui button primary" type="submit">@lang('misc.create')</button>
        <a href="{{ url()->previous(route('bar.overview')) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
