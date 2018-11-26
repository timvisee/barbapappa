@extends('layouts.app')

@php
    use \App\Http\Controllers\EconomyCurrencyController;
@endphp

@section('content')
    <h2 class="ui header">{{ $economy->name }}</h2>

    {!! Form::open(['action' => ['EconomyController@doEdit', $community->human_id, $economy->id], 'method' => 'PUT', 'class' => 'ui form']) !!}
        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', $economy->name, ['placeholder' => __('pages.economies.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        @if(perms(EconomyCurrencyController::permsView()))
            <div class="ui divider hidden"></div>

            <div class="ui top attached vertical menu fluid">
                <h5 class="ui item header">
                    @lang('misc.currencies')
                    ({{ $currencies->count() }})
                </h5>
                @forelse($currencies as $currency)
                    <a class="item" href="{{ route('community.economy.currency.show', [
                        'communityId' => $community->id,
                        'economyId' => $economy->id,
                        'economyCurrencyId' => $currency->id
                    ]) }}">
                        {{ $currency->displayName}}
                    </a>
                @endforeach
            </div>
            <a href="{{ route('community.economy.currency.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                    class="ui bottom attached button">
                @lang('misc.manage')
            </a>

            <div class="ui divider hidden"></div>
        @endif

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
