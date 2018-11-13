@extends('layouts.app')

@php
    use \App\Http\Controllers\EconomyCurrencyController;
@endphp

@section('content')
    <h2 class="ui header">{{ $currency->name }}</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.enabled')</td>
                <td>{{ yesno($currency->enabled) }}</td>
            </tr>
            <tr>
                <td>@lang('misc.currency')</td>
                <td>
                    {{ $currency->name }}
                    ({{ $currency->symbol }})
                </td>
            </tr>
            <tr>
                <td>@lang('pages.supportedCurrencies.allowWallets')</td>
                <td>{{ yesno($currency->allow_wallet) }}</td>
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>{{ $currency->created_at }}</td>
            </tr>
            @if($currency->created_at != $currency->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>{{ $currency->updated_at }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(perms(EconomyCurrencyController::permsManage()))
        <p>
            <a href="{{ route('community.economy.currency.edit', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'supportedCurrencyId' => $currency->id]) }}"
                    class="ui button basic secondary">
                @lang('misc.change')
            </a>
            <a href="{{ route('community.economy.currency.delete', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'supportedCurrencyId' => $currency->id]) }}"
                    class="ui button basic negative">
                @lang('misc.remove')
            </a>
        </p>
    @endif

    <a href="{{ route('community.economy.currency.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
            class="ui button basic">
        @lang('general.goBack')
    </a>
@endsection
