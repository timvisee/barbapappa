@extends('layouts.app')

@section('content')
    <h2 class="ui header">{{ $currency->name }}</h2>

    {!! Form::open(['action' => ['EconomyCurrencyController@doEdit', $community->human_id, $economy->id, $currency->id], 'method' => 'PUT', 'class' => 'ui form']) !!}
        <div class="ui message">
            <div class="header">@lang('pages.supportedCurrencies.enabledTitle')</div>
            <p>@lang('pages.supportedCurrencies.enabledDescription')</p>
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('enabled') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="enabled"
                        tabindex="0"
                        class="hidden"
                        {{ $currency->enabled ? 'checked="checked"' : '' }}>
                {{ Form::label('enabled', __('misc.enabled')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enabled') }}
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('pages.supportedCurrencies.changeCurrencyTitle')</div>
            <p>@lang('pages.supportedCurrencies.changeCurrencyDescription')</p>
        </div>

        <div class="field disabled">
            {{ Form::label('currency', __('misc.currency')) }}

            <div class="ui fluid selection dropdown">
                <input type="hidden" name="currency" value="{{ $currency->currency->id }}" />
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.unspecified')</div>
                <div class="menu">
                    <div class="item" data-value="{{ $currency->currency->id }}">{{ $currency->currency->displayName }}</div>
                </div>
            </div>
        </div>

        <div class="ui divider"></div>

        <div class="ui message">
            <div class="header">@lang('pages.supportedCurrencies.allowWallets')</div>
            <p>@lang('pages.supportedCurrencies.allowWalletsDescription')</p>
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('allow_wallet') ? 'error' : '' }}">
            <div class="ui checkbox">
                <input type="checkbox"
                        name="allow_wallet"
                        tabindex="0"
                        class="hidden"
                        {{ $currency->allow_wallet ? 'checked="checked"' : '' }}>
                {{ Form::label('allow_wallet', __('pages.supportedCurrencies.allowWallets')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('allow_wallet') }}
        </div>

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('community.economy.currency.show', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'supportedCurrencyId' => $currency->id]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
