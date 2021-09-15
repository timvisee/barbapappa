@extends('layouts.app')

@section('title', __('pages.economies.zeroAllWallets'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.economies.zeroAllWalletsQuestion')</p>

    <br />

    {!! Form::open(['action' => ['EconomyWalletController@doZeroWallets', 'communityId' => $community->human_id, 'economyId' => $economy->id], 'method' => 'POST', 'class' => 'ui form']) !!}

        <div class="field {{ ErrorRenderer::hasError('description') ? 'error' : '' }}">
            {{ Form::label('description', __('misc.description') . ':') }}
            {{ Form::text('description', __('pages.economies.zeroAllWalletsDescription')) }}
            {{ ErrorRenderer::inline('description') }}
        </div>

        <div class="ui hidden divider"></div>

        <div class="ui top attached warning message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('pages.wallets.modifyBalanceWarning', ['app' => config('app.name')])
        </div>

        <div class="ui attached warning message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('misc.cannotBeUndone')
        </div>

        {{-- Confirmation field --}}
        <div class="ui attached segment">
            <div class="required field {{ ErrorRenderer::hasError('confirm_text') ? 'error' : '' }}">
                {{ Form::label('confirm_text', __('misc.typeToVerify', ['text' => __('pages.economies.zeroAllWalletsConfirmText')]) . ':') }}
                {{ Form::text('confirm_text', '', ['placeholder' => __('pages.economies.zeroAllWalletsConfirmText')]) }}
                {{ ErrorRenderer::inline('confirm_text') }}
                <br />
                {{ ErrorRenderer::inline('confirm_text') }}
            </div>
        </div>

        {{-- Confirmation checkbox --}}
        <div class="ui bottom attached segment">
            <div class="required field {{ ErrorRenderer::hasError('confirm') ? 'error' : '' }}">
                <div class="ui checkbox">
                    {{ Form::checkbox('confirm', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                    {{ Form::label('confirm', __('misc.iUnderstandContinue')) }}
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
