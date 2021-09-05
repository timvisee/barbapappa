@extends('layouts.app')

@section('title', __('pages.economies.zeroAllWallets'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.economies.zeroAllWalletsQuestion')</p>

    <div class="ui warning message visible">
        <span class="halflings halflings-warning-sign"></span>
        @lang('misc.cannotBeUndone')
    </div>

    <br />

    {!! Form::open(['action' => ['EconomyWalletController@doZeroWallets', 'communityId' => $community->human_id, 'economyId' => $economy->id], 'method' => 'POST', 'class' => 'ui form']) !!}
        <div class="ui buttons">
            <a href="{{ route('community.economy.wallets.overview', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesContinue')</button>
        </div>
    {!! Form::close() !!}
@endsection
