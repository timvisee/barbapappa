@extends('layouts.app')

@section('title', __('pages.wallets.topUp'))

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
        'WalletController@doTopUp',
        $community->human_id,
        $economy->id,
        $wallet->id,
    ], 'method' => 'POST', 'class' => 'ui form']) !!}

    <div class="ui two item menu">
        <a href="{{ route('community.wallet.topUp', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'walletId' => $wallet->id
        ]) }}"
            class="item active">@lang('misc.deposit')</a>
        <a href="#" class="item disabled">@lang('misc.withdraw')</a>
    </div>

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

    @php
        // Build list of amounts to top-up with
        $cur = $wallet->balance;
        $default_amounts = [5, 10, 20, 50, 100];
        $amounts = [];

        foreach($default_amounts as $add)
            if($cur + $add >= 0)
                $amounts[] = $add;

        if(($to_zero = -$wallet->balance) > 0)
            array_unshift($amounts, $to_zero);

        array_unique($amounts);
    @endphp

    <div class="grouped fields {{ ErrorRenderer::hasError('amount') ? 'error' : '' }}">
        {{ Form::label('amount', __('pages.paymentService.amountToTopUpInCurrency', ['currency' => $currency->name]) . ':') }}

        @foreach($amounts as $amount)
            <div class="field">
                <div class="ui radio checkbox">
                    {{ Form::radio('amount', $amount, ($wallet->balance + $amount) == 0, ['class' => 'hidden', 'tabindex' => 0]) }}
                    <label for="custom_amount">
                        @lang('pages.paymentService.pay')
                        &nbsp;
                        {!! $currency->format($amount, BALANCE_FORMAT_COLOR) !!}
                        &nbsp;
                        <span class="subtle">
                            @lang('misc.to')
                            {!! $currency->format($wallet->balance + $amount) !!}
                        </span>
                    </label>
                </div>
            </div>
        @endforeach
        <div class="field">
            <div class="ui radio checkbox">
                {{ Form::radio('amount', '', false, ['class' => 'hidden', 'tabindex' => 0]) }}
                {{ Form::label('amount', __('pages.paymentService.otherPay') . ':') }}
            </div>
        </div>

        <div class="field {{ ErrorRenderer::hasError('amount_custom') ? 'error' : '' }}" style="margin-left: 2em;">
            <div class="ui labeled input">
                {{ Form::label('amount_custom', $currency->symbol, ['class' => 'ui label']) }}
                {{ Form::text('amount_custom', '', ['id' => 'amount_custom', 'placeholder' => '1.23']) }}
            </div>
            {{ ErrorRenderer::inline('amount_custom') }}
        </div>

        {{ ErrorRenderer::inline('amount') }}
    </div>

    <div class="ui hidden divider"></div>

    <div class="grouped fields {{ ErrorRenderer::hasError('payment_service') ? 'error' : '' }}">
        {{ Form::label('payment_service', __('pages.paymentService.selectPaymentServiceToUse') . ':') }}
        @foreach($services as $service)
            <div class="field">
                <div class="ui radio checkbox">
                    {{ Form::radio('payment_service', $service->id, false, ['class' => 'hidden', 'tabindex' => 0]) }}
                    {{ Form::label('payment_service', $service->displayName() .  ' (' . $service->serviceable->__('duration') . ')') }}
                </div>
            </div>
        @endforeach
        {{ ErrorRenderer::inline('payment_service') }}
    </div>

    <div class="ui hidden divider"></div>

    <p>
        <button class="ui button primary"
            type="submit">@lang('pages.wallets.topUp')</button>
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
