@extends('layouts.app')

@section('title', __('pages.bar.editBar'))
@php
    $menusection = 'bar_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['BarController@doEdit', $bar->human_id], 'method' => 'PUT', 'class' => 'ui form']) !!}

        <div class="required field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', $bar->name, ['placeholder' => __('pages.bar.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <div class="field {{ ErrorRenderer::hasError('description') ? 'error' : '' }}">
            {{ Form::label('description', __('misc.description') . ' (' . __('misc.public') . '):') }}
            {{ Form::textarea('description', $bar->description, ['placeholder' => __('pages.bar.descriptionPlaceholder'), 'rows' => 3]) }}
            {{ ErrorRenderer::inline('description') }}
        </div>

        <div class="ui divider"></div>

        <div class="inline field {{ ErrorRenderer::hasError('enabled') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('enabled', true, $bar->enabled, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('enabled', __('general.enabled')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enabled') }}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('misc.slug')</div>
            <p>@lang('pages.bar.slugDescription')</p>

            <p>
                @lang('pages.bar.slugDescriptionExample')</br>
                <u><code>{{ URL::to('/b/' . $bar->id) }}</code></u>
                <span class="glyphicons glyphicons-chevron-right"></span>
                <u><code>{{ URL::to('/b/' . ($bar->slug ?? __('pages.bar.slugPlaceholder'))) }}</code></u>.
            </p>
        </div>

        <div class="field {{ ErrorRenderer::hasError('slug') ? 'error' : '' }}">
            {{ Form::label('slug', __('misc.slug') . ':') }}
            <div class="ui labeled input">
                {{ Form::label('slug', '/b/', ['class' => 'ui label basic']) }}
                {{ Form::text('slug', $bar->slug, ['placeholder' => __('pages.bar.slugPlaceholder')]) }}
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
            {{ Form::text('password', $bar->password, ['placeholder' => __('misc.codePlaceholder')]) }}
            {{ ErrorRenderer::inline('password') }}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('pages.community.economy')</div>
            <p>@lang('pages.bar.economyDescription')</p>
        </div>

        <div class="required field disabled {{ ErrorRenderer::hasError('economy') ? 'error' : '' }}">
            {{ Form::label('economy', __('pages.community.economy')) }}

            <div class="ui fluid selection dropdown">
                {{ Form::hidden('economy', $bar->economy_id) }}
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
        </div>

        <div class="field {{ ErrorRenderer::hasError('inventory') ? 'error' : '' }}">
            {{ Form::label('inventory', __('pages.inventories.inventory')) }}

            <div class="ui fluid selection dropdown">
                {{ Form::hidden('inventory', $bar->inventory_id) }}
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.pleaseSpecify')</div>
                <div class="menu">
                    <div class="item" data-value=""><i>@lang('misc.none')</i></div>
                    @foreach($bar->economy->inventories as $i)
                        <div class="item" data-value="{{ $i->id }}">{{ $i->name }}</div>
                    @endforeach
                </div>
            </div>

            {{ ErrorRenderer::inline('inventory') }}
        </div>

        <a href="{{ route('community.economy.inventory.index', ['communityId' => $community->human_id, $bar->economy_id]) }}">
            @lang('pages.inventories.manage')
        </a>

        <div class="ui divider"></div>

        <div class="field {{ ErrorRenderer::hasError('low_balance_text') ? 'error' : '' }}">
            {{ Form::label('low_balance_text', __('pages.bar.lowBalanceText') . ':') }}
            {{ Form::textarea('low_balance_text', $bar->low_balance_text, ['placeholder' => __('pages.bar.lowBalanceTextPlaceholder'), 'rows' => 3]) }}
            {{ ErrorRenderer::inline('low_balance_text') }}
        </div>

        <div class="ui divider"></div>

        <div class="inline field {{ ErrorRenderer::hasError('show_explore') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('show_explore', true, $bar->show_explore, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('show_explore', __('pages.bar.showExploreDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('show_explore') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('show_community') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('show_community', true, $bar->show_community, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('show_community', __('pages.bar.showCommunityDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('show_community') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('self_enroll') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('self_enroll', true, $bar->self_enroll, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('self_enroll', __('pages.bar.selfEnrollDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('self_enroll') }}
        </div>

        <div class="ui divider"></div>

        <div class="inline field {{ ErrorRenderer::hasError('show_history') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('show_history', true, $bar->show_history, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('show_history', __('pages.bar.showHistoryDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('show_history') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('show_tallies') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('show_tallies', true, $bar->show_tallies, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('show_tallies', __('pages.bar.showTalliesDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('show_tallies') }}
        </div>

        <br />

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
