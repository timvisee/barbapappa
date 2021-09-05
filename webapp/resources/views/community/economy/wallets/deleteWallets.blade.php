@extends('layouts.app')

@section('title', __('pages.economies.deleteAllWallets'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.economies.deleteAllWalletsQuestion')</p>

    {!! Form::open(['action' => ['EconomyWalletController@doDeleteWallets', 'communityId' => $community->human_id, 'economyId' => $economy->id], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="ui hidden divider"></div>

        <div class="ui top attached warning message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('misc.cannotBeUndone')
        </div>

        <div class="ui bottom attached segment">
            <div class="required field {{ ErrorRenderer::hasError('confirm') ? 'error' : '' }}">
                <div class="ui checkbox">
                    {{ Form::checkbox('confirm', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                    {{ Form::label('confirm', __('pages.economies.confirmDeleteAllWallets')) }}
                </div>
                <br />
                {{ ErrorRenderer::inline('confirm') }}
            </div>
        </div>

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
