@extends('layouts.app')

@section('title', $wallet->name)

@php
    use \App\Http\Controllers\EconomyController;
    use \App\Models\Wallet;
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.wallet.index', ['communityId' => $community->id]) }}">
                {{ $community->name }}
            </a>
            @lang('misc.for')
            <a href="{{ route('community.wallet.list', ['communityId' => $community->id, 'economyId' => $economy->id]) }}">
                {{ $economy->name }}
            </a>
        </div>
    </h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>{{ $wallet->name }}</td>
            </tr>
            <tr>
                <td>@lang('misc.balance')</td>
                <td>{!! $wallet->formatBalance(BALANCE_FORMAT_COLOR) !!}</td>
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $wallet->created_at])</td>
            </tr>
            @if($wallet->created_at != $wallet->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $wallet->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    <p>
        <div class="ui buttons">
            <a href="{{ route('community.wallet.edit', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]) }}"
                    class="ui button secondary">
                @lang('misc.rename')
            </a>
            <a href="{{ route('community.wallet.delete', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]) }}"
                    class="ui button negative">
                @lang('misc.delete')
            </a>
        </div>
    </p>

    {{-- Transaction list --}}
    @include('transaction.include.list', [
        'groups' => [[
            'header' => trans_choice('pages.transactions.last#', count($transactions)),
            'transactions' => $transactions,
        ]],
        'button' => [
            'label' => __('misc.showAll'),
            'link' => route('community.wallet.transactions', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'walletId' => $wallet->id
            ]),
        ],
    ])

    <p>
        <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
