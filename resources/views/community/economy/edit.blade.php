@extends('layouts.app')

@section('content')
    <h2 class="ui header">{{ $economy->name }}</h2>

    {!! Form::open(['action' => ['EconomyController@doEdit', $community->human_id, $economy->id], 'method' => 'PUT', 'class' => 'ui form']) !!}
        <div class="field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', $economy->name, ['placeholder' => __('pages.economies.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
