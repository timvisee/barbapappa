@extends('layouts.app')

@section('title', __('pages.inventories.editInventory'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open([
        'action' => [
            'InventoryController@doEdit',
            $community->human_id,
            $economy->id,
            $inventory->id,
        ],
        'method' => 'PUT',
        'class' => 'ui form'
    ]) !!}
        <div class="required field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', $inventory->name, ['placeholder' => __('pages.inventories.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('community.economy.inventory.show', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'inventoryId' => $inventory->id,
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
