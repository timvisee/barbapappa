@extends('layouts.app')

@section('title', __('pages.balanceImport.newSystem'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open([
        'action' => [
            'BalanceImportSystemController@doCreate',
            $community->human_id,
            $economy->id,
        ],
        'method' => 'POST',
        'class' => 'ui form'
    ]) !!}
        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', '', ['placeholder' => __('pages.balanceImport.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <button class="ui button primary" type="submit" name="submit" value="">
            @lang('misc.add')
        </button>
        <a href="{{ route('community.economy.balanceimport.index', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
