@extends('layouts.app')

@section('title', __('pages.payments.requiringAction'))

@php
    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.payments.backToPayments'),
        'link' => route('payment.approveList'),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.payments.paymentRequiresCommunityAction')</p>

    {{-- Embed approval step view --}}
    @include($stepView)

    <p>
        <a href="{{ route('payment.approveList') }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
