@extends('layouts.app')

@section('title', __('pages.payments.paymentsToApprove'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.payments.paymentsToApproveDescription')</p>

    {{-- Payment list --}}
    @include('payment.include.listApprove', [
        'groups' => [[
                'header' => trans_choice('pages.payments.requiringAction#', count($payments)),
            'payments' => $payments,
        ]],
    ])
@endsection
