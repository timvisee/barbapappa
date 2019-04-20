@extends('layouts.app')

@section('title', $wallet->name)

@php
    use \App\Models\Wallet;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

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

    {{-- TODO: complete this transaction view --}}
    <div class="ui top attached vertical menu fluid">
        <h5 class="ui item header">
            {{ trans_choice('pages.transactions.last#', count($transactions)) }}
        </h5>
        @forelse($transactions as $transaction)
            <a class="item"
                    href="{{ route('transaction.show', [
                        'transactionId' => $transaction->id,
                    ]) }}">
                {{ $transaction->describe() }}
                {!! $transaction->formatCost(BALANCE_FORMAT_LABEL); !!}
            </a>
        @endforeach
    </div>
    <a href="{{ route('community.economy.currency.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
            class="ui bottom attached button">
        @lang('misc.showAll')
    </a>
    <br />

    <p>
        <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
