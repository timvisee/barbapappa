@extends('layouts.app')

@section('title', __('pages.balanceImportEvent.editEvent'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open([
        'action' => [
            'BalanceImportEventController@doEdit',
            $community->human_id,
            $economy->id,
            $system->id,
            $event->id,
        ],
        'method' => 'PUT',
        'class' => 'ui form'
    ]) !!}
        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', $event->name, ['placeholder' => __('pages.balanceImportEvent.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <button class="ui button primary" type="submit">@lang('misc.rename')</button>
        <a href="{{ route('community.economy.balanceimport.event.show', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'systemId' => $system->id,
            'eventId' => $event->id,
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
