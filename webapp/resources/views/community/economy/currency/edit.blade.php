@extends('layouts.app')

@section('title', $currency->name)

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['EconomyCurrencyController@doEdit', $community->human_id, $economy->id, $currency->id], 'method' => 'PUT', 'class' => 'ui form']) !!}
        <div class="ui message">
            <div class="header">@lang('pages.currencies.enabledTitle')</div>
            <p>@lang('pages.currencies.enabledDescription')</p>
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('enabled') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('enabled', true, $currency->enabled, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('enabled', __('misc.enabled')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enabled') }}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('pages.currencies.changeCurrencyTitle')</div>
            <p>@lang('pages.currencies.changeCurrencyDescription')</p>
        </div>

        <div class="field disabled">
            {{ Form::label('currency', __('misc.currency')) }}

            <div class="ui fluid selection dropdown">
                {{ Form::hidden('currency', $currency->currency->id) }}
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.pleaseSpecify')</div>
                <div class="menu">
                    <div class="item" data-value="{{ $currency->currency->id }}">{{ $currency->currency->displayName }}</div>
                </div>
            </div>
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('pages.currencies.allowWallets')</div>
            <p>@lang('pages.currencies.allowWalletsDescription')</p>
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('allow_wallet') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('allow_wallet', true, $currency->allow_wallet, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('allow_wallet', __('pages.currencies.allowWallets')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('allow_wallet') }}
        </div>

        <div class="ui divider hidden"></div>

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('community.economy.currency.show', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'economyCurrencyId' => $currency->id
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
