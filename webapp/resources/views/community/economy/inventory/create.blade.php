@extends('layouts.app')

@section('title', __('pages.inventories.createInventory'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open([
        'action' => [
            'InventoryController@doCreate',
            $community->human_id,
            $economy->id,
        ],
        'method' => 'POST',
        'class' => 'ui form'
    ]) !!}
        <div class="required field {{ ErrorRenderer::hasError('name') ? 'error' : '' }}">
            {{ Form::label('name', __('misc.name') . ':') }}
            {{ Form::text('name', '', ['placeholder' => __('pages.inventories.namePlaceholder')]) }}
            {{ ErrorRenderer::inline('name') }}
        </div>

        <button class="ui button primary" type="submit" name="submit" value="">
            @lang('misc.create')
        </button>

        <a href="{{ route('community.economy.inventory.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
