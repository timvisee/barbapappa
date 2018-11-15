@extends('layouts.app')

@section('content')
    <h2 class="ui header">{{ $currency->name }}</h2>
    <p>@lang('pages.supportedCurrencies.deleteQuestion')</p>

    <p>@lang('pages.supportedCurrencies.deleteVoidNotice')</p>

    <a href="{{ route('community.economy.currency.edit', ['communityId' => $community->id, 'economyId' => $economy->id, 'supportedCurrencyId' => $currency->id]) }}">
        @lang('pages.supportedCurrencies.change')
    </a>

    <br />

    <div class="ui warning message visible">
        <span class="halflings halflings-warning-sign"></span>
        @lang('misc.cannotBeUndone')
    </div>

    <br />

    {!! Form::open(['action' => ['EconomyCurrencyController@doDelete', 'communityId' => $community->human_id, 'economyId' => $economy->id, 'supportedCurrencyId' => $currency->id], 'method' => 'DELETE', 'class' => 'ui form']) !!}
        <div class="ui buttons">
            <a href="{{ route('community.economy.currency.show', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'supportedCurrencyId' => $currency->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesRemove')</button>
        </div>
    {!! Form::close() !!}
@endsection
