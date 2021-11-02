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
        @if($hasQuantity)
            <div class="ui info message visible">
                <span class="halflings halflings-info-sign icon"></span>
                @lang('pages.inventories.moveBeforeDelete')

                <p>
                    <a href="{{ route('community.economy.inventory.move', [
                                'communityId' => $community->human_id,
                                'economyId' => $economy->id,
                                'inventoryId' => $inventory->id,
                                'all' => true,
                            ]) }}"
                            class="ui basic button">
                        @lang('pages.inventories.moveProducts')
                    </a>
                </p>
            </div>
        @endif

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
            <button class="ui button positive basic" type="submit">@lang('general.yesDelete')</button>
        </div>
    {!! Form::close() !!}
@endsection
