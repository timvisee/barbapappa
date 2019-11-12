@extends('layouts.app')

@section('title', __('pages.payments.details'))

@php
    use \BarPay\Models\Payment;

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

    <div class="ui divider hidden"></div>

    {{-- Payment state icon --}}
    <div class="ui one small statistics">
        @switch($payment->state)
            @case(Payment::STATE_INIT)
            @case(Payment::STATE_PENDING_COMMUNITY)
            @case(Payment::STATE_PENDING_AUTO)
                <div class="statistic yellow">
                    <div class="value">
                        <span class="halflings halflings-hourglass" title="{{ $payment->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @case(Payment::STATE_PENDING_USER)
                <div class="statistic orange">
                    <div class="value">
                        <span class="halflings halflings-user" title="{{ $payment->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @case(Payment::STATE_PROCESSING)
                <div class="statistic yellow">
                    <div class="value">
                        <span class="halflings halflings-refresh" title="{{ $payment->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @case(Payment::STATE_COMPLETED)
                <div class="statistic green">
                    <div class="value">
                        <span class="halflings halflings-ok" title="{{ $payment->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @case(Payment::STATE_REVOKED)
            @case(Payment::STATE_REJECTED)
                <div class="statistic red">
                    <div class="value">
                        <span class="halflings halflings-remove" title="{{ $payment->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @case(Payment::STATE_FAILED)
                <div class="statistic red">
                    <div class="value">
                        <span class="halflings halflings-alert" title="{{ $payment->stateName() }}"></span>
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
                @break

            @default
                <div class="statistic">
                    <div class="value">
                        {{ $payment->stateName() }}
                    </div>
                    <div class="label">@lang('misc.state')</div>
                </div>
        @endswitch
    </div>
    <br>
    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                {!! $payment->formatCost(BALANCE_FORMAT_COLOR) !!}
            </div>
            <div class="label">@lang('misc.amount')</div>
        </div>
    </div>

    <div class="ui divider hidden"></div>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.state')</td>
                <td>{{ $payment->stateName() }}</td>
            </tr>
            <tr>
                <td>@lang('pages.paymentService.serviceType')</td>
                <td>{{ $payment->service->displayName() }}</td>
            </tr>
            @if($payment->user_id == barauth()->getUser()->id)
                <tr>
                    <td>@lang('misc.user')</td>
                    <td>{{ $payment->user->name }}</td>
                </tr>
            @endif
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
        </tbody>
    </table>

    <div class="ui divider hidden"></div>

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

    <div class="ui divider hidden"></div>

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

    <p>
        <a class="ui button primary"
                href="{{ route('dashboard') }}"
                title="@lang('pages.dashboard.title')">
            @lang('pages.dashboard.title')
        </a>
        <a href="{{ route('payment.index') }}"
                class="ui button basic">
            @lang('pages.payments.backToPayments')
        </a>
    </p>
@endsection
