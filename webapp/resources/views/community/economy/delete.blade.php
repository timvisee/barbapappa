@extends('layouts.app')

@section('title', $economy->name)
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    @if($blockingWallets->isNotEmpty())
        <p>@lang('pages.economies.deleteBlocked')</p>

        {{-- Delete blockers --}}
        <div class="ui top vertical menu fluid">
            <h5 class="ui item header">@lang('pages.wallets.title')</h5>
            @foreach($blockingWallets as $wallet)
                <a class="item" href="{{ route('community.wallet.show', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'walletId' => $wallet->id,
                        ]) }}">
                    {{ $wallet->name }}
                </a>
            @endforeach
        </div>

        <a href="{{ route('community.economy.show', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id
                ]) }}" class="ui button basic">
            @lang('pages.economies.backToEconomy')
        </a>
    @else
        <p>@lang('pages.economies.deleteQuestion')</p>

        <div class="ui warning message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('misc.cannotBeUndone')
        </div>

        <br />

        {!! Form::open(['action' => ['EconomyController@doDelete', 'communityId' => $community->human_id, 'economyId' => $economy->id], 'method' => 'DELETE', 'class' => 'ui form']) !!}
            <div class="ui buttons">
                <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                        class="ui button negative">
                    @lang('general.noGoBack')
                </a>
                <div class="or" data-text="@lang('general.or')"></div>
                <button class="ui button positive basic" type="submit">@lang('general.yesRemove')</button>
            </div>
        {!! Form::close() !!}
    @endif
@endsection
