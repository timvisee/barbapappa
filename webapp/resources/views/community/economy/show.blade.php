@extends('layouts.app')

@section('title', $economy->name)

@php
    use \App\Http\Controllers\EconomyController;
    use \App\Http\Controllers\EconomyCurrencyController;
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
                <td>{{ $economy->created_at }}</td>
            </tr>
            @if($economy->created_at != $economy->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>{{ $economy->updated_at }}</td>
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
    </p>

    @if(perms(EconomyCurrencyController::permsView()))
        <div class="ui divider hidden"></div>

        @include('community.economy.include.currencyList', [
            'header' => __('misc.currencies') . ' (' .  $currencies->count() . ')',
            'currencies' => $currencies,
            'button' => [
                'label' => __('misc.manage'),
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
            @lang('pages.economies.all')
        </a>
    </p>
@endsection
