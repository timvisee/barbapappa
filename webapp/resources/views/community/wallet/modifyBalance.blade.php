@extends('layouts.app')

@section('title', __('pages.wallets.modifyBalance'))

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.wallet.show', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]) }}">
                {{ $wallet->name }}
            </a>
            @lang('misc.in')
            <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}">
                {{ $economy->name }}
            </a>
        </div>
    </h2>

    {!! Form::open(['action' => [
        'WalletController@doModifyBalance',
        $community->human_id,
        $economy->id,
        $wallet->id,
    ], 'method' => 'POST', 'class' => 'ui form']) !!}

    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                {!! $wallet->formatBalance(BALANCE_FORMAT_COLOR) !!}
            </div>
            <div class="label">@lang('misc.balance')</div>
        </div>
    </div>

    <div class="ui hidden divider"></div>

    <div class="grouped fields {{ ErrorRenderer::hasError('modifyMethod') ? 'error' : '' }}">
        {{ Form::label('modifyMethod', __('pages.wallets.modifyMethod', ['currency' => $currency->name]) . ':') }}

        <div class="field">
            <div class="ui radio checkbox">
                {{ Form::radio('modifyMethod', 'deposit', false, ['class' => 'hidden', 'tabindex' => 0]) }}
                <label for="modifyMethod">@lang('pages.wallets.modifyMethodDeposit')</label>
            </div>
        </div>
        <div class="field">
            <div class="ui radio checkbox">
                {{ Form::radio('modifyMethod', 'withdraw', false, ['class' => 'hidden', 'tabindex' => 0]) }}
                <label for="modifyMethod">@lang('pages.wallets.modifyMethodWithdraw')</label>
            </div>
        </div>
        <div class="field">
            <div class="ui radio checkbox">
                {{ Form::radio('modifyMethod', 'set', false, ['class' => 'hidden', 'tabindex' => 0]) }}
                <label for="modifyMethod">@lang('pages.wallets.modifyMethodSet')</label>
            </div>
        </div>

        {{ ErrorRenderer::inline('modifyMethod') }}
    </div>

    <div class="ui hidden divider"></div>

    <div class="required field {{ ErrorRenderer::hasError('amount') ? 'error' : '' }}">
        {{ Form::label('amount', __('pages.paymentService.amountInCurrency', ['currency' => $currency->name]) . ':') }}
        <div class="ui labeled input">
            {{ Form::label('amount', $currency->symbol, ['class' => 'ui label']) }}
            {{ Form::text('amount', '', ['id' => 'amount', 'placeholder' => '1.23']) }}
        </div>
        {{ ErrorRenderer::inline('amount') }}
    </div>

    <div class="field {{ ErrorRenderer::hasError('description') ? 'error' : '' }}">
        {{ Form::label('description', __('misc.description') . ':') }}
        {{ Form::text('description', '') }}
        {{ ErrorRenderer::inline('description') }}
    </div>

    <div class="ui hidden divider"></div>

    <div class="ui top attached warning message visible">
        <span class="halflings halflings-warning-sign"></span>
        @lang('pages.wallets.modifyBalanceWarning', ['app' => config('app.name')])
    </div>

    <div class="ui bottom attached segment">
        <div class="required field {{ ErrorRenderer::hasError('confirm') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('confirm', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('confirm', __('pages.wallets.confirmModifyBalance')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('confirm') }}
        </div>
    </div>

    <div class="ui hidden divider"></div>

    <p>
        <button class="ui button primary"
            type="submit">@lang('pages.wallets.modifyBalance')</button>
        <a href="{{ route('community.wallet.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id,
                ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    </p>

    {!! Form::close() !!}
@endsection
