@extends('layouts.app')

@section('title', __('pages.wallets.transferToSelf'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.wallet.show', $community, $wallet);
    $menusection = 'community';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui two item menu">
        <a href="{{ route('community.wallet.transfer', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'walletId' => $wallet->id
        ]) }}"
            class="item active">@lang('pages.wallets.toSelf')</a>
        <a href="{{ route('community.wallet.transfer.user',
            ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]
        ) }}"
            class="item">@lang('pages.wallets.toUser')</a>
    </div>

    <div class="ui hidden divider"></div>

    {!! Form::open(['action' => [
        'WalletController@doTransfer',
        $community->human_id,
        $economy->id,
        $wallet->id,
    ], 'method' => 'POST', 'class' => 'ui form']) !!}

    <div class="ui hidden divider"></div>

    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                {!! $wallet->formatBalance(BALANCE_FORMAT_COLOR) !!}
            </div>
            <div class="label">@lang('misc.balance')</div>
        </div>
    </div>

    <div class="ui hidden divider"></div>

    <div class="required field {{ ErrorRenderer::hasError('amount') ? 'error' : '' }}">
        {{ Form::label('amount', __('misc.amountInCurrency', ['currency' => $currency->name]) . ':') }}
        <div class="ui labeled input">
            {{ Form::label('amount', $currency->symbol, ['class' => 'ui label']) }}
            {{ Form::text('amount', '', ['id' => 'amount', 'inputmode' => 'decimal', 'placeholder' => '1.23']) }}
        </div>
        {{ ErrorRenderer::inline('amount') }}
    </div>

    <div class="required field {{ ErrorRenderer::hasError('to_wallet') ? 'error' : '' }}">
        {{ Form::label('to_wallet', __('pages.wallets.toSelf') . ':') }}

        <div class="ui fluid selection dropdown">
            {{ Form::hidden('to_wallet') }}
            <i class="dropdown icon"></i>

            <div class="default text">@lang('misc.pleaseSpecify')</div>
            <div class="menu">
                @foreach($toWallets as $toWallet)
                    <div class="item" data-value="{{ $toWallet->id }}">{{ $toWallet->name }}</div>
                @endforeach
                <div class="item" data-value="new"><i>@lang('pages.wallets.createWallet')</i></div>
            </div>
        </div>

        {{ ErrorRenderer::inline('to_wallet') }}
    </div>

    <div class="ui hidden divider"></div>

    <p>
        <button class="ui button primary"
            type="submit">@lang('pages.wallets.transfer')</button>
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
