@extends('layouts.app')

@section('title', __('pages.payments.title'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.payments.description')</p>

    @if($requireCommunityAction)
        <div class="ui info message visible">
            <div class="header">@lang('pages.payments.handleCommunityPayments')</div>
            <p>@lang('pages.payments.paymentsWaitingForAction')</p>
            <a href="{{ route('payment.approveList') }}"
                    class="ui button basic">
                @lang('pages.payments.handlePayments')
            </a>
        </div>
    @endif

    {{-- Payment list --}}
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
    @endif

    @include('payment.include.list', [
        'groups' => [[
            'header' => trans_choice('pages.payments.settled#', count($settled)),
            'payments' => $settled,
        ]],
    ])
@endsection
