@extends('layouts.app')

@section('title', __('pages.bar.createBar'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['BarController@doCreate', 'communityId' => $community->human_id], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="required field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', '', ['placeholder' => __('pages.bar.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="field {{ ErrorRenderer::hasError('description') ? 'error' : '' }}">
            {{ Form::label('description', __('misc.description') . ' (' .  __('misc.public') . '):') }}
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
            {{ Form::label('slug', __('misc.slug') . ':') }}
            <div class="ui labeled input">
                {{ Form::label('slug', '/b/', ['class' => 'ui label']) }}
                {{ Form::text('slug', '', ['placeholder' => __('pages.bar.slugPlaceholder')]) }}
            </div>
            {{ ErrorRenderer::inline('slug') }}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('misc.code')</div>
            <p>@lang('pages.bar.codeDescription')</p>
        </div>

        <div class="field {{ ErrorRenderer::hasError('password') ? 'error' : '' }}">
            {{ Form::label('password', __('misc.code') . ':') }}
            {{ Form::text('password', $community->password, ['placeholder' => __('misc.codePlaceholder')]) }}
            {{ ErrorRenderer::inline('password') }}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('pages.community.economy')</div>
            <p>@lang('pages.bar.economyDescription')</p>
        </div>

        <div class="required field {{ ErrorRenderer::hasError('economy') ? 'error' : '' }}">
            {{ Form::label('economy', __('pages.community.economy')) }}

            <div class="ui fluid selection dropdown">
                {{ Form::hidden('economy') }}
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

        <div class="ui message">
            <div class="header">@lang('pages.inventories.inventory')</div>
            <p>@lang('pages.bar.inventoryDescription')</p>
            <p>@lang('pages.bar.selectInventoryAfterCreate')</p>
        </div>

        <div class="required field disabled {{ ErrorRenderer::hasError('inventory') ? 'error' : '' }}">
            {{ Form::label('inventory', __('pages.inventories.inventory')) }}

            <div class="ui fluid selection dropdown">
                {{ Form::hidden('inventory', null) }}
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.pleaseSpecify')</div>
                <div class="menu"></div>
            </div>

            {{ ErrorRenderer::inline('inventory') }}
        </div>

        <div class="ui divider"></div>

        <div class="field {{ ErrorRenderer::hasError('low_balance_text') ? 'error' : '' }}">
            {{ Form::label('low_balance_text', __('pages.bar.lowBalanceText') . ':') }}
            {{ Form::textarea('low_balance_text', '', ['placeholder' => __('pages.bar.lowBalanceTextPlaceholder'), 'rows' => 3]) }}
            {{ ErrorRenderer::inline('low_balance_text') }}
        </div>

        <div class="ui divider"></div>

        <div class="inline field {{ ErrorRenderer::hasError('show_explore') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('show_explore', true, $community->show_explore, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('show_explore', __('pages.bar.showExploreDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('show_explore') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('show_community') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('show_community', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('show_community', __('pages.bar.showCommunityDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('show_community') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('self_enroll') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('self_enroll', true, $community->self_enroll, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('self_enroll', __('pages.bar.selfEnrollDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('self_enroll') }}
        </div>

        <div class="ui divider"></div>

        <div class="inline field">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('join', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
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
