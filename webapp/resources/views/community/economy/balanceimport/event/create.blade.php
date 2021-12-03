@extends('layouts.app')

@section('title', __('pages.balanceImportEvent.newEvent'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open([
        'action' => [
            'BalanceImportEventController@doCreate',
            $community->human_id,
            $economy->id,
            $system->id,
        ],
        'method' => 'POST',
        'class' => 'ui form'
    ]) !!}
        <div class="required field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', now()->toDateString(), ['placeholder' => __('pages.balanceImportEvent.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <button class="ui button primary" type="submit" name="submit" value="">
            @lang('misc.add')
        </button>
        <a href="{{ route('community.economy.balanceimport.event.index', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'systemId' => $system->id,
                ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
