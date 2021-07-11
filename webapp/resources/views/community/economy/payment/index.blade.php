@extends('layouts.app')

@section('title', __('pages.economyPayments.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.payment.index', $economy);
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header bar-header">
        @yield('title')
    </h2>

    <p>@lang('pages.economyPayments.description')</p>

    <div class="ui hidden divider"></div>

    @include('payment.include.list', [
        'groups' => [[
            'payments' => $payments,
            'showUser' => true,
        ]],
    ])
    {{ $payments->links() }}

    <p>
        <div class="ui floating right labeled icon dropdown button">
            <i class="dropdown icon"></i>
            @lang('misc.manage')
            <div class="menu">
                <a href="{{ route('community.economy.payment.export', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                        ]) }}"
                        class="item">
                    @lang('misc.export')
                </a>
            </div>
        </div>

        <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('pages.economies.backToEconomy')
        </a>
    </p>
@endsection
