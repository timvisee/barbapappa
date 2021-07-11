@extends('layouts.app')

@section('title', __('pages.payments.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.payment.index', $economy);
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header bar-header">
        @yield('title')
    </h2>

    <p>@lang('pages.economies.paymentsDescription')</p>

    <div class="ui hidden divider"></div>

    @include('payment.include.list', [
        'groups' => [[
            'payments' => $payments,
            'showUser' => true,
        ]],
    ])
    {{ $payments->links() }}

    <p>
        <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('pages.economies.backToEconomy')
        </a>
    </p>
@endsection
