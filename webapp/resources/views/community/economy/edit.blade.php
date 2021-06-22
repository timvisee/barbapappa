@extends('layouts.app')

@section('title', $economy->name)
@php
    $menusection = 'community_manage';

    use \App\Http\Controllers\CurrencyController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => ['EconomyController@doEdit', $community->human_id, $economy->id], 'method' => 'PUT', 'class' => 'ui form']) !!}
        <div class="required field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', $economy->name, ['placeholder' => __('pages.economies.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        @if(perms(CurrencyController::permsView()))
            <div class="ui divider hidden"></div>

            @include('community.economy.include.currencyList', [
                'header' => __('misc.currencies') . ' (' .  $currencies->count() . ')',
                'currencies' => $currencies,
                'button' => [
                    'label' => __('misc.manage'),
                    'link' => route('community.economy.currency.index', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id
                    ]),
                ],
            ])

            <div class="ui divider hidden"></div>
        @endif

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
