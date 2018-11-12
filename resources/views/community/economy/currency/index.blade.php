@extends('layouts.app')

@php
    use \App\Http\Controllers\EconomyCurrencyController;
@endphp

@section('content')
    <h2 class="ui header">@lang('pages.supportedCurrencies.title') ({{ count($currencies) }})</h2>
    <p>@lang('pages.supportedCurrencies.description')</p>

    <div class="ui vertical menu fluid">
        @forelse($currencies as $currency)
            <a href="{{ route('community.economy.currency.show', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'supportedCurrencyId' => $currency->id]) }}" class="item">
                {{ $currency->currency->name }}
                ({{ $currency->currency->symbol }})
            </a>
        @empty
            <div class="item">
                <i>@lang('pages.supportedCurrencies.noCurrencies')</i>
            </div>
        @endforelse
    </div>

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
