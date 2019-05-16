@extends('layouts.app')

@section('title', __('pages.products.title'))

@php
    use \App\Http\Controllers\ProductController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.economies.backToEconomy'),
        'link' => route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.economy.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                    ]) }}">
                {{ $economy->name }}
            </a>
        </div>
    </h2>

    {{-- TODO: show product list here! --}}
    {{-- Product list --}}
    @include('community.economy.product.include.list', [
        'groups' => [[
            {{-- 'header' => __('pages.wallets.walletTransactions') . ' (' .  count($products) . ')', --}}
            'products' => $products,
        ]],
    ])

    <p>
        @if(perms(ProductController::permsManage()))
            <a href="{{ route('community.economy.product.create', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                    class="ui button basic positive">
                @lang('misc.add')
            </a>
        @endif

        <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('pages.economies.backToEconomy')
        </a>
    </p>
@endsection
