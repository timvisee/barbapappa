@extends('layouts.app')

@section('title', $currency->name)
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.currency.show', $currency);
    $menusection = 'community_manage';

    use App\Http\Controllers\CurrencyController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.enabled')</td>
                <td>{{ yesno($currency->enabled) }}</td>
            </tr>
            <tr>
                <td>@lang('misc.currency')</td>
                <td>{{ $currency->displayName }}</td>
            </tr>
            @if($currency->code)
                <tr>
                    <td>@lang('pages.currencies.code')</td>
                    <td>{{ $currency->code }}</td>
                </tr>
            @endif
            <tr>
                <td>@lang('pages.currencies.exampleNotation')</td>
                <td>{{ $currency->format(1234.56) }}</td>
            </tr>
            <tr>
                <td>@lang('pages.currencies.allowWallets')</td>
                <td>{{ yesno($currency->allow_wallet) }}</td>
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $currency->created_at])</td>
            </tr>
            @if($currency->created_at != $currency->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $currency->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    <p>
        @if(perms(CurrencyController::permsManage()))
            <div class="ui buttons">
                <a href="{{ route('community.economy.currency.edit', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'currencyId' => $currency->id
                ]) }}"
                        class="ui button secondary">
                    @lang('misc.change')
                </a>
                <a href="{{ route('community.economy.currency.delete', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'currencyId' => $currency->id
                ]) }}"
                        class="ui button negative">
                    @lang('misc.remove')
                </a>
            </div>
        @endif
    </p>

    <p>
        <a href="{{ route('community.economy.currency.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
