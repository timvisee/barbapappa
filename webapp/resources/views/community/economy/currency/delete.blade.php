@extends('layouts.app')

@section('title', $currency->name)
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.currencies.deleteQuestion')</p>

    <p>@lang('pages.currencies.deleteVoidNotice')</p>

    <a href="{{ route('community.economy.currency.edit', [
        'communityId' => $community->human_id,
        'economyId' => $economy->id,
        'currencyId' => $currency->id
    ]) }}">
        @lang('pages.currencies.change')
    </a>

    <br />

    <div class="ui warning message visible">
        <span class="halflings halflings-warning-sign"></span>
        @lang('misc.cannotBeUndone')
    </div>

    <br />

    {!! Form::open(['action' => [
        'CurrencyController@doDelete',
        'communityId' => $community->human_id,
        'economyId' => $economy->id,
        'currencyId' => $currency->id
    ], 'method' => 'DELETE', 'class' => 'ui form']) !!}
        <div class="ui buttons">
            <a href="{{ route('community.economy.currency.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'currencyId' => $currency->id
            ]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesRemove')</button>
        </div>
    {!! Form::close() !!}
@endsection
