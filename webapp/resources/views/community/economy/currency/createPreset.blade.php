@extends('layouts.app')

@section('title', __('pages.currencies.createCurrency') . ': ' . $preset['name'])
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['CurrencyController@doAddPreset', $community->human_id, $economy->id], 'method' => 'POST', 'class' => 'ui form']) !!}
        {{ Form::hidden('code', $code) }}

        <p>@lang('pages.currencies.enabledDescription')</p>

        <div class="inline field {{ ErrorRenderer::hasError('enabled') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('enabled', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('enabled', __('misc.enabled')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enabled') }}
        </div>

        <div class="ui section divider"></div>

        <p>@lang('pages.currencies.allowWalletsDescription')</p>

        <div class="inline field {{ ErrorRenderer::hasError('allow_wallet') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('allow_wallet', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('allow_wallet', __('pages.currencies.allowWallets')) }} </div>
            <br />
            {{ ErrorRenderer::inline('allow_wallet') }}
        </div>

        <div class="ui divider hidden"></div>

        <button class="ui button primary" type="submit">@lang('misc.add')</button>
        <a href="{{ route('community.economy.currency.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
