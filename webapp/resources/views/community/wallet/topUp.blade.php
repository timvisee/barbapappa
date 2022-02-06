@extends('layouts.app')

@section('title', __('pages.wallets.topUp'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.wallet.show', $community, $wallet);
    $menusection = 'community';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    @if(!$redemption)
        <div class="ui two item menu">
            <a href="{{ route('community.wallet.topUp', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'walletId' => $wallet->id
            ]) }}"
                class="item active">@lang('misc.deposit')</a>
            <a href="#" class="item disabled">@lang('misc.withdraw')</a>
        </div>
    @endif

    {!! Form::open(['action' => [
        'WalletController@doTopUp',
        $community->human_id,
        $economy->id,
        $wallet->id,
    ], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="ui hidden divider"></div>

        <div class="ui hidden divider"></div>

        <div class="ui one small statistics">
            <div class="statistic">
                <div class="value">
                    {!! $wallet->formatBalance(BALANCE_FORMAT_COLOR) !!}
                </div>
                <div class="label">@lang('misc.balance')</div>
            </div>
        </div>

        {{-- Show spending estimate --}}
        @if($montly_costs != null && !$montly_costs->isZero())
            <p class="align-center">
                <em>@lang('pages.paymentService.youSpendAboutEachMonth', ['amount' => $montly_costs->formatAmount()])</em>
            </p>
        @else
            <div class="ui hidden divider"></div>
        @endif

        {{-- Amount selection --}}
        <div class="ui vertical menu fluid field {{ ErrorRenderer::hasError('amount') ? 'error' : '' }}">
            <h5 class="ui item header">
                @lang('pages.paymentService.amountToTopUpInCurrency', ['currency' => $currency->name]):
            </h5>

            @foreach($amounts as $amount)
                <div class="item inline {{ ErrorRenderer::hasError('amount') ? 'error' : '' }}">
                    <div class="ui radio checkbox">
                        {{ Form::radio('amount', $amount['amount'], $amount['selected'] ?? false, ['class' => 'hidden', 'tabindex' => 0]) }}
                        <label for="amount">
                            @lang('pages.paymentService.pay')
                            {!! $currency->format($amount['amount'], BALANCE_FORMAT_COLOR) !!}
                        </label>

                    </div>

                    @if(isset($amount['sum']))
                        <span class="ui label">
                            @lang('misc.to')
                            {!! $currency->format($amount['sum']) !!}
                        </span>
                    @endif

                    @if(isset($amount['note']))
                        <span class="ui teal label">
                            {{ $amount['note'] }}
                        </span>
                    @endif
                </div>
            @endforeach

            {{-- Custom top-up amount if not top-upping for redemption --}}
            @if(!$redemption)
                <div class="item inline {{ ErrorRenderer::hasError('amount') ? 'error' : '' }}">
                    <div class="ui radio checkbox">
                        {{ Form::radio('amount', '', false, ['class' => 'hidden', 'tabindex' => 0]) }}
                        <label for="amount">@lang('pages.paymentService.pay')</label>
                    </div>

                    <div class="field checkbox-list-inline-field {{ ErrorRenderer::hasError('amount_custom') ? 'error' : '' }}">
                        <div class="ui inline labeled input">
                            {{ Form::label('amount_custom', $currency->symbol, ['class' => 'ui label']) }}
                            {{ Form::text('amount_custom', '', ['id' => 'amount_custom', 'inputmode' => 'decimal', 'placeholder' => '1.23']) }}
                        </div>
                    </div>
                </div>
            @endif

            @if($redemption)
                <div class="item">
                    @lang('general.or') <a href="{{ route('community.wallet.topUp', [
                        'communityId' => $economy->community_id,
                        'economyId' => $economy->id,
                        'walletId' => $wallet->id,
                    ]) }}">{{ strtolower(__('pages.paymentService.topUpWithLargerAmount')) }}</a>
                </div>
            @endif
        </div>
        {{ ErrorRenderer::alert('amount') }}
        {{ ErrorRenderer::alert('amount_custom') }}

        {{-- Payment service selection --}}
        <div class="ui vertical menu fluid field {{ ErrorRenderer::hasError('payment_service') ? 'error' : '' }}">
            <h5 class="ui item header">
                @lang('pages.paymentService.selectPaymentServiceToUse'):
            </h5>

            @foreach($services as $service)
                <div class="item inline {{ ErrorRenderer::hasError('payment_service') ? 'error' : '' }}">
                    <div class="ui radio checkbox">
                        {{ Form::radio('payment_service', $service->id, false, ['class' => 'hidden', 'tabindex' => 0]) }}
                        {{ Form::label('payment_service', $service->displayName()) }}
                    </div>

                    <span class="ui orange label">
                        {{ $service->serviceable->__('duration') }}
                    </span>
                </div>
            @endforeach
        </div>
        {{ ErrorRenderer::alert('payment_service') }}

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
