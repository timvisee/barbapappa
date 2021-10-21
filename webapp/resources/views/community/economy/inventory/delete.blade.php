@extends('layouts.app')

@section('title', $inventory->name)
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.inventories.deleteQuestion')</p>

    {!! Form::open([
        'action' => [
            'InventoryController@doDelete',
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'inventoryId' => $inventory->id,
        ],
        'method' => 'DELETE',
        'class' => 'ui form'
    ]) !!}
        <div class="ui warning message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('misc.cannotBeUndone')
        </div>

        <br />

        <div class="ui buttons">
            <a href="{{ route('community.economy.inventory.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'inventoryId' => $inventory->id,
                    ]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesRemove')</button>
        </div>
    {!! Form::close() !!}
@endsection
