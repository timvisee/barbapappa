@extends('layouts.app')

@php
    use \App\Http\Controllers\EconomyCurrencyController;
@endphp

@section('content')
    <h2 class="ui header">@lang('pages.supportedCurrencies.title') ({{ count($enabled) }})</h2>
    <p>@lang('pages.supportedCurrencies.description')</p>

    <h3 class="ui header">@lang('misc.enabled')</h3>
    <div class="ui vertical menu fluid">
        @forelse($enabled as $currency)
            <a href="{{ route('community.economy.currency.show', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'supportedCurrencyId' => $currency->id]) }}" class="item">
                {{ $currency->displayName }}
            </a>
        @empty
            <div class="item">
                <i>@lang('pages.supportedCurrencies.noCurrencies')</i>
            </div>
        @endforelse
    </div>

    @if($disabled->isNotEmpty())
        <h3 class="ui header">@lang('misc.disabled')</h3>
        <div class="ui vertical menu fluid">
            @forelse($disabled as $currency)
                <a href="{{ route('community.economy.currency.show', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'supportedCurrencyId' => $currency->id]) }}" class="item">
                    {{ $currency->displayName }}
                </a>
            @empty
                <div class="item">
                    <i>@lang('pages.supportedCurrencies.noCurrencies')</i>
                </div>
            @endforelse
        </div>
    @endif

    @if(perms(EconomyCurrencyController::permsManage()))
        <a href="{{ route('community.economy.currency.create', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic positive">
            @lang('misc.add')
        </a>
    @endif

    <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
            class="ui button basic">
        @lang('general.goBack')
    </a>
@endsection
