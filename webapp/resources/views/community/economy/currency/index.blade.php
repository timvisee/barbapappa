@extends('layouts.app')

@section('title', __('pages.currencies.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.currency.index', $economy);
@endphp

@php
    use \App\Http\Controllers\CurrencyController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.economies.backToEconomy'),
        'link' => route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.currencies.description')</p>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.enabled') ({{ count($enabled) }})</h5>
        @forelse($enabled as $currency)
            <a href="{{ route('community.economy.currency.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'currencyId' => $currency->id
            ]) }}" class="item">
                {{ $currency->displayName }}
            </a>
        @empty
            <div class="item">
                <i>@lang('pages.currencies.noCurrencies')</i>
            </div>
        @endforelse
    </div>

    @if($disabled->isNotEmpty())
        <div class="ui vertical menu fluid">
            <h5 class="ui item header">@lang('misc.disabled') ({{ count($disabled) }})</h5>
            @forelse($disabled as $currency)
                <a href="{{ route('community.economy.currency.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'currencyId' => $currency->id
                ]) }}" class="item">
                    {{ $currency->displayName }}
                </a>
            @empty
                <div class="item">
                    <i>@lang('pages.currencies.noCurrencies')</i>
                </div>
            @endforelse
        </div>
    @endif

    @if(perms(CurrencyController::permsManage()))
        <a href="{{ route('community.economy.currency.create', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic positive">
            @lang('misc.add')
        </a>
    @endif

    <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
            class="ui button basic">
        @lang('pages.economies.backToEconomy')
    </a>
@endsection
