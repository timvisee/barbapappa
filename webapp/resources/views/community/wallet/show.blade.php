@extends('layouts.app')

@php
    use \App\Models\Wallet;
@endphp

@section('content')
    <h2 class="ui header">{{ $wallet->name }}</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>{{ $wallet->name }}</td>
            </tr>
            <tr>
                <td>Balance</td>
                <td>{!! $wallet->formatBalance(null, Wallet::BALANCE_COLOR) !!}</td>
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>{{ $wallet->created_at }}</td>
            </tr>
            @if($wallet->created_at != $wallet->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>{{ $wallet->updated_at }}</td>
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

    <p>
        <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
