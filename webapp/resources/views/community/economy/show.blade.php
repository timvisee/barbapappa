@extends('layouts.app')

@section('title', $economy->name)

@php
    use \App\Http\Controllers\EconomyController;
    use \App\Http\Controllers\EconomyCurrencyController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.economies.backToEconomies'),
        'link' => route('community.economy.index', ['communityId' => $community->human_id]),
        'icon' => 'undo',
    ];
    $menulinks[] = [
        'name' => __('pages.currencies.manage'),
        'link' => route('community.economy.currency.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id
        ]),
        'icon' => 'currency-conversion',
    ];
    $menulinks[] = [
        'name' => __('pages.products.manageProducts'),
        'link' => route('community.economy.product.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id
        ]),
        'icon' => 'shopping-bag',
    ];
    $menulinks[] = [
        'name' => __('pages.paymentService.manageServices'),
        'link' => route('community.economy.payservice.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id
        ]),
        'icon' => 'credit-card',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>{{ $economy->name }}</td>
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $economy->created_at])</td>
            </tr>
            @if($economy->created_at != $economy->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $economy->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    <p>
        @if(perms(EconomyController::permsManage()))
            <div class="ui buttons">
                <a href="{{ route('community.economy.edit', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                        class="ui button secondary">
                    @lang('misc.edit')
                </a>
                <a href="{{ route('community.economy.delete', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                        class="ui button negative">
                    @lang('misc.delete')
                </a>
            </div>
        @endif

        <a href="{{ route('community.economy.product.index', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id
                ]) }}"
                class="ui button basic">
            @lang('pages.products.manageProducts')
        </a>

        <a href="{{ route('community.economy.payservice.index', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id
                ]) }}"
                class="ui button basic">
            @lang('pages.paymentService.manageServices')
        </a>

        <a href="{{ route('community.economy.balanceimport.index', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id
                ]) }}"
                class="ui button basic">
            @lang('pages.balanceImport.manageSystems')
        </a>
    </p>

    @if(perms(EconomyCurrencyController::permsView()))
        <div class="ui divider hidden"></div>

        @include('community.economy.include.currencyList', [
            'header' => __('misc.currencies') . ' (' .  $currencies->count() . ')',
            'currencies' => $currencies,
            'button' => [
                'label' => __('pages.currencies.manage'),
                'link' => route('community.economy.currency.index', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id
                ]),
            ],
        ])

        <div class="ui divider hidden"></div>
    @endif

    <p>
        <a href="{{ route('community.economy.index', ['communityId' => $community->human_id]) }}"
                class="ui button basic">
            @lang('pages.economies.backToEconomies')
        </a>
    </p>
@endsection
