@extends('layouts.app')

@section('title', __('pages.bar.createBar'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['BarController@doCreate', 'communityId' => $community->human_id], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', '', ['placeholder' => __('pages.bar.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="field {{ ErrorRenderer::hasError('description') ? 'error' : '' }}">
            {{ Form::label('description', __('misc.description') . ' (' .  __('misc.public') . ', ' .  __('general.optional') . '):') }}
            {{ Form::textarea('description', '', ['placeholder' => __('pages.bar.descriptionPlaceholder'), 'rows' => 3]) }}
            {{ ErrorRenderer::inline('description') }}
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
            {{ Form::text('password', $community->password, ['placeholder' => __('misc.codePlaceholder')]) }}
            {{ ErrorRenderer::inline('password') }}
        </div>

        <div class="ui divider"></div>

        <div class="field {{ ErrorRenderer::hasError('economy') ? 'error' : '' }}">
            {{ Form::label('economy', __('pages.community.economy')) }}

            <div class="ui fluid selection dropdown">
                <input type="hidden" name="economy">
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.pleaseSpecify')</div>
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

        <div class="inline field {{ ErrorRenderer::hasError('show_explore') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="show_explore"
                        tabindex="0"
                        class="hidden"
                        {{ $community->show_explore ? 'checked="checked"' : '' }}>
                {{ Form::label('show_explore', __('pages.bar.showExploreDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('show_explore') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('show_community') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="show_community"
                        tabindex="0"
                        class="hidden"
                        checked="checked">
                {{ Form::label('show_community', __('pages.bar.showCommunityDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('show_community') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('self_enroll') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="self_enroll"
                        tabindex="0"
                        class="hidden"
                        {{ $community->self_enroll ? 'checked="checked"' : '' }}>
                {{ Form::label('self_enroll', __('pages.bar.selfEnrollDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('self_enroll') }}
        </div>

        <div class="ui divider"></div>

        <div class="inline field">
            <div class="ui toggle checkbox">
                <input type="checkbox" name="join" tabindex="0" class="hidden" checked="checked">
                {{ Form::label('join', __('pages.bar.joinAfterCreate')) }}
            </div>
            {{ ErrorRenderer::inline('join') }}
        </div>

        <br />

        <button class="ui button primary" type="submit">@lang('misc.create')</button>
        <a href="{{ url()->previous(route('community.manage', ['communityId' => $community->human_id])) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
