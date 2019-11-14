@extends('layouts.app')

@section('title', __('pages.currencies.createCurrency'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['NewCurrencyController@doCreate', $community->human_id, $economy->id], 'method' => 'POST', 'class' => 'ui form']) !!}
        <p>@lang('pages.currencies.enabledDescription')</p>

        <div class="inline field {{ ErrorRenderer::hasError('enabled') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('enabled', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('enabled', __('misc.enabled')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enabled') }}
        </div>

        <div class="ui section divider"></div>

        <p>@lang('pages.currencies.detailDescription')</p>

        <p>@lang('pages.currencies.nameDescription')</p>
        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', '', ['placeholder' => __('pages.currencies.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>
        <div class="ui divider hidden"></div>

        <p>@lang('pages.currencies.codeDescription')</p>
        <div class="ui list">
            <a href="https://en.wikipedia.org/wiki/ISO_4217"
                    target="_blank"
                    class="item">
                Wikipedia: ISO 4217
            </a>
        </div>
        <div class="field {{ ErrorRenderer::hasError('code') ? 'error' : '' }}">
            {{ Form::label('code', __('pages.currencies.code') . ' (' .  __('general.optional') . '):') }}
            {{ Form::text('code', '', ['placeholder' => __('pages.currencies.codePlaceholder')]) }}
            {{ ErrorRenderer::inline('code') }}
        </div>
        <div class="ui divider hidden"></div>

        <p>@lang('pages.currencies.symbolDescription')</p>
        <div class="field {{ ErrorRenderer::hasError('symbol') ? 'error' : '' }}">
            {{ Form::label('symbol', __('misc.symbol') . ':') }}
            {{ Form::text('symbol', '', ['placeholder' => __('pages.currencies.symbolPlaceholder')]) }}
            {{ ErrorRenderer::inline('symbol') }}
        </div>
        <div class="ui divider hidden"></div>

        <p>@lang('pages.currencies.formatDescription', [
            'app' => config('app.name')
        ])</p>
        <div class="field {{ ErrorRenderer::hasError('format') ? 'error' : '' }}">
            {{ Form::label('format', __('pages.currencies.format') . ':') }}
            {{ Form::text('format', '', ['placeholder' => __('pages.currencies.formatPlaceholder')]) }}
            {{ ErrorRenderer::inline('format') }}
        </div>

        <div class="ui section divider"></div>

        <p>@lang('pages.currencies.allowWalletsDescription')</p>

        <div class="inline field {{ ErrorRenderer::hasError('allow_wallet') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('allow_wallet', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('allow_wallet', __('pages.currencies.allowWallets')) }} </div>
            <br />
            {{ ErrorRenderer::inline('allow_wallet') }}
        </div>

        <div class="ui divider hidden"></div>

        <button class="ui button primary" type="submit">@lang('misc.add')</button>
        <a href="{{ route('community.economy.currency.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
