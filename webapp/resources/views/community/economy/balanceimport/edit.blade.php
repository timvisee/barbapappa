@extends('layouts.app')

@section('title', __('pages.balanceImport.editSystem'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open([
        'action' => [
            'BalanceImportSystemController@doEdit',
            $community->human_id,
            $economy->id,
            $system->id,
        ],
        'method' => 'PUT',
        'class' => 'ui form'
    ]) !!}
        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', $system->name, ['placeholder' => __('pages.balanceImport.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <button class="ui button primary" type="submit">@lang('misc.rename')</button>
        <a href="{{ route('community.economy.balanceimport.show', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'systemId' => $system->id,
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
