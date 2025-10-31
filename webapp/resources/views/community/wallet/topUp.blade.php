@extends('layouts.app')

@section('title', __('pages.wallets.topUp'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.wallet.show', $community, $wallet);
    $menusection = 'community';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    @if(!isset($amount) || $amount == 0)

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

        {{-- Pending payment list --}}
        @php
            $groups = [];
            if($requireUserAction->isNotEmpty())
                $groups[] = [
                    'header' => trans_choice('pages.payments.requiringAction#', count($requireUserAction)),
                    'payments' => $requireUserAction,
                ];
            if($inProgress->isNotEmpty())
                $groups[] = [
                    'header' => trans_choice('pages.payments.inProgress#', count($inProgress)),
                    'payments' => $inProgress,
                ];
        @endphp
        @if(!empty($groups))
            @include('payment.include.list', [
                'groups' => $groups,
            ])
            <div class="ui hidden divider"></div>
        @endif

        <div class="ui hidden divider"></div>

        <p>
            <div class="ui one small statistics">
                <div class="statistic">
                    <div class="value">
                        {!! $wallet->formatBalance(BALANCE_FORMAT_COLOR) !!}
                    </div>
                    <div class="label">@lang('misc.balance')</div>
                </div>
            </div>
        </p>

        {{-- Show spending estimate --}}
        @if($montly_costs != null && !$montly_costs->isZero())
            <p class="align-center">
                <em>@lang('pages.paymentService.youSpendAboutEachMonth', ['amount' => $montly_costs->formatAmount()])</em>
            </p>
        @else
            <div class="ui hidden divider"></div>
        @endif

        {{-- Amount selection --}}
        @if(!empty($amounts))
            <div class="ui vertical menu fluid field {{ ErrorRenderer::hasError('amount') ? 'error' : '' }}">
                <h5 class="ui item header">
                    @lang('pages.paymentService.amountToTopUp'):
                </h5>

                @foreach($amounts as $amount)
                    <a href="{{ route('community.wallet.topUp', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'walletId' => $wallet->id,
                        'amount' => $amount['amount'],
                    ]) }}" class="item inline {{ ErrorRenderer::hasError('amount') ? 'error' : '' }}">
                        @lang('pages.paymentService.pay')
                        <i class="chevron right icon"></i>

                        {!! $currency->format($amount['amount'], BALANCE_FORMAT_COLOR) !!}

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
                    </a>
                @endforeach

                <div class="item inline">

                    {!! Form::open(['action' => [
                        'WalletController@doTopUp',
                        $community->human_id,
                        $economy->id,
                        $wallet->id,
                    ], 'method' => 'GET', 'class' => 'ui form']) !!}
                        <span>@lang('pages.wallets.payCustomAmount')</span><br><br>
                        <div class="ui form inline">
                                <div class="field {{ ErrorRenderer::hasError('amount') ? 'error' : '' }}">
                                    <div class="ui labeled action input">
                                        {{ Form::label('amount', $currency->symbol, ['class' => 'ui basic label']) }}
                                        {{ Form::text('amount', '', ['id' => 'amount', 'inputmode' => 'decimal', 'placeholder' => '1.23']) }}
                                        <button class="ui icon button basic"
                                            type="submit"><i class="chevron right icon"></i></button> 
                                    </div>
                                </div>
                        </div>
                        {{ ErrorRenderer::alert('amount') }}
                    {!! Form::close() !!}

                </div>
            </div>
        @else
            <p class="align-center">
                @lang('pages.wallets.walletBalanceSettled')!
            </p>
            <div class="ui hidden divider"></div>
        @endif

        <a href="{{ route('community.wallet.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id,
                ]) }}"
                class="fluid ui button basic">
            @lang('general.cancel')
        </a>

    @elseif(!isset($service))

        <div class="ui hidden divider"></div>
        <p>
            <div class="ui one small statistics">
                <div class="statistic">
                    <div class="label">@lang('misc.pay')</div>
                    <div class="value">
                        {!! $wallet->currency->format($amount, BALANCE_FORMAT_COLOR) !!}
                    </div>
                </div>
            </div>
        </p>
        <div class="ui hidden divider"></div>

        {{-- Payment service selection --}}
        <div class="ui vertical menu fluid field {{ ErrorRenderer::hasError('payment_service') ? 'error' : '' }}">
            <h5 class="ui item header">
                @lang('pages.paymentService.selectPaymentServiceToUse'):
            </h5>

            @foreach($services as $service)
                <a href="{{ route('community.wallet.topUp', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id,
                    'amount' => $amount,
                    'method' => $service->id,
                ]) }}" class="item inline {{ ErrorRenderer::hasError('payment_service') ? 'error' : '' }}">
                    {{ $service->displayName() }}

                    <i class="chevron right icon"></i>

                    <span class="ui orange label">
                        {{ $service->serviceable->__('duration') }}
                    </span>
                </a>
            @endforeach
        </div>

        <a href="{{ route('community.wallet.topUp', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id,
                ]) }}"
                class="fluid ui button basic">
            @lang('general.goBack')
        </a>

    @else

        <div class="ui hidden divider"></div>
        <p>
            <div class="ui one small statistics">
                <div class="statistic">
                    <div class="label">@lang('misc.pay')</div>
                    <div class="value">
                        {!! $wallet->currency->format($amount, BALANCE_FORMAT_COLOR) !!}
                    </div>
                    <div class="label">
                        @lang('misc.via') {!! $service->displayName() !!}
                    </div>
                </div>
            </div>
        </p>
        <div class="ui hidden divider"></div>

        {!! Form::open(['action' => [
            'WalletController@doTopUp',
            $community->human_id,
            $economy->id,
            $wallet->id,
        ], 'method' => 'POST', 'class' => 'ui form']) !!}

            <p>
                <p>
                    <button class="fluid ui huge button positive"
                        type="submit">@lang('misc.pay')</button>
                </p>
                <a href="{{ route('community.wallet.topUp', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'walletId' => $wallet->id,
                            'amount' => $amount,
                        ]) }}"
                        class="fluid ui button basic">
                    @lang('general.goBack')
                </a>
            </p>

            {{ Form::hidden('amount', $amount) }}
            {{ Form::hidden('payment_service', $service->id) }}

        {!! Form::close() !!}

    @endif

@endsection
