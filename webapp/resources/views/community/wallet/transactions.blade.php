@extends('layouts.app')

@section('title', __('pages.wallets.walletTransactions'))

@section('content')
    <h2 class="ui header">
        @yield('title')
        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.wallet.show', [
                        'communityId' => $community->id,
                        'economyId' => $economy->id,
                        'walletId' => $wallet->id,
                    ]) }}">
                {{ $wallet->name }}
            </a>
        </div>
    </h2>

    {{-- TODO: implement pagination --}}
    {{-- TODO: complete this transaction view --}}
    <div class="ui top vertical menu fluid">
        <h5 class="ui item header">
            @lang('pages.wallets.walletTransactions') ({{ count($transactions) }})
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

    <p>
        <a href="{{ route('community.wallet.show', [
                    'communityId' => $community->id,
                    'economyId' => $economy->id,
                    'walletId' => $wallet->id,
                ]) }}"
                class="ui button basic">
            @lang('pages.wallets.backToWallet')
        </a>
    </p>
@endsection
