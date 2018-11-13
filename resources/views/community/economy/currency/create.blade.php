@extends('layouts.app')

@section('content')
    <h2 class="ui header">@lang('pages.supportedCurrencies.createCurrency')</h2>

    {!! Form::open(['action' => ['EconomyCurrencyController@doCreate', $community->human_id, $economy->id], 'method' => 'POST', 'class' => 'ui form']) !!}

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
                        checked="checked">
                {{ Form::label('enabled', __('misc.enabled')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enabled') }}
        </div>

        <div class="ui divider"></div>

        <div class="field {{ ErrorRenderer::hasError('currency') ? 'error' : '' }}">
            {{ Form::label('currency', __('misc.currency')) }}

            <div class="ui fluid selection dropdown">
                <input type="hidden" name="currency" value="{{ $currencies->first()->id }}" />
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.unspecified')</div>
                <div class="menu">
                    @foreach($currencies as $c)
                        <div class="item" data-value="{{ $c->id }}">{{ $c->displayName }}</div>
                    @endforeach
                </div>
            </div>

            {{ ErrorRenderer::inline('currency') }}
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
                        checked="checked">
                {{ Form::label('allow_wallet', __('pages.supportedCurrencies.allowWallets')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('allow_wallet') }}
        </div>

        <button class="ui button primary" type="submit">@lang('misc.add')</button>
        <a href="{{ route('community.economy.currency.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
