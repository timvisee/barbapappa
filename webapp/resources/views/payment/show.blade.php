@extends('layouts.app')

@section('title', __('pages.payments.details'))

@php
    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.payments.backToPayments'),
        'link' => route('payment.index'),
        'icon' => 'undo',
    ];

    if($payment->isInProgress())
        $menulinks[] = [
            'name' => __('misc.showProgress'),
            'link' => route('payment.pay', [
                'paymentId' => $payment->id,
            ]),
            'icon' => 'hourglass',
        ];

    if($payment->canCancel())
        $menulinks[] = [
            'name' => __('pages.payments.cancelPayment'),
            'link' => route('payment.cancel', [
                'paymentId' => $payment->id,
            ]),
            'icon' => 'remove-sign',
        ];

    if(!empty($transaction))
        $menulinks[] = [
            'name' => __('pages.transactions.viewTransaction'),
            'link' => route('transaction.show', [
                'transactionId' => $transaction->id,
            ]),
            'icon' => 'shopping-bag',
        ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    @if($payment->isInProgress())
        <div class="ui info message visible">
            <div class="header">@lang('pages.payments.inProgress')</div>
            <p>@lang('pages.payments.inProgressDescription')</p>
            <a href="{{ route('payment.pay', ['paymentId' => $payment->id]) }}"
                    class="ui button basic">
                @lang('misc.showProgress')
            </a>
        </div>
    @endif

    <table class="ui compact celled definition table">
        <tbody>
            @if($payment->user_id == barauth()->getUser()->id)
                <tr>
                    <td>@lang('misc.user')</td>
                    <td>{{ $payment->user->name }}</td>
                </tr>
            @endif
            <tr>
                <td>@lang('misc.amount')</td>
                <td>{!! $payment->formatCost(BALANCE_FORMAT_COLOR) !!}</td>
            </tr>
            <tr>
                <td>@lang('misc.state')</td>
                <td>{{ $payment->stateName() }}</td>
            </tr>
            <tr>
                <td>@lang('misc.initiatedAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $payment->created_at])</td>
            </tr>
            @if($payment->created_at != $payment->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $payment->updated_at])</td>
                </tr>
            @endif
            <tr>
                <td>@lang('pages.paymentService.serviceType')</td>
                <td>{{ $payment->service->displayName() }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Action buttons --}}
    @if($payment->isInProgress())
        <div class="ui buttons">
            @if($payment->isInProgress())
                <a href="{{ route('payment.pay', ['paymentId' => $payment->id]) }}"
                        class="ui button positive">
                    @lang('misc.progress')
                </a>
            @endif
            @if($payment->canCancel())
                <a href="{{ route('payment.cancel', ['paymentId' => $payment->id]) }}"
                        class="ui button negative">
                    @lang('general.cancel')
                </a>
            @endif
        </div>
    @endif

    {{-- Show link to transaction --}}
    @if(!empty($transaction))
        <div class="ui top vertical menu fluid">
            <h5 class="ui item header">
                @lang('pages.transactions.linkedTransaction')
            </h5>

            <a class="item"
                    href="{{ route('transaction.show', [
                        'transactionId' => $transaction->id,
                    ])}}">
                {{ $transaction->describe() }}
                <span class="subtle">
                    ({{ $transaction->stateName() }})
                </span>

                {!! $transaction->formatCost(BALANCE_FORMAT_LABEL) !!}

                <span class="sub-label">
                    @include('includes.humanTimeDiff', ['time' => $transaction->updated_at ?? $transaction->created_at])
                </span>
            </a>
        </div>
    @endif

    <a href="{{ route('payment.index') }}"
            class="ui button basic">
        @lang('general.goBack')
    </a>
@endsection
