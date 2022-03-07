@extends('layouts.app')

@section('title', __('pages.inventories.moveProducts'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.inventory.show', $inventory);
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.inventories.moveDescription')</p>

    {!! Form::open([
        'action' => [
            'InventoryController@doMove',
            $community->human_id,
            $economy->id,
            $inventory->id,
        ],
        'method' => 'PUT',
        'class' => 'ui form'
    ]) !!}
        <div class="two fields">
            <div class="required field {{ ErrorRenderer::hasError('inventory_from') ? 'error' : '' }}">
                {{ Form::label('inventory_from', __('pages.inventories.fromInventory')) }}

                <div class="ui fluid selection dropdown">
                    {{ Form::hidden('inventory_from', $from_id) }}
                    <i class="dropdown icon"></i>

                    <div class="default text">@lang('misc.pleaseSpecify')</div>
                    <div class="menu">
                        @foreach($economy->inventories as $i)
                            <div class="item" data-value="{{ $i->id }}">{{ $i->name }}</div>
                        @endforeach
                    </div>
                </div>

                {{ ErrorRenderer::inline('inventory_from') }}
            </div>

            <div class="required field {{ ErrorRenderer::hasError('inventory_to') ? 'error' : '' }}">
                {{ Form::label('inventory_to', __('pages.inventories.toInventory')) }}

                <div class="ui fluid selection dropdown">
                    {{ Form::hidden('inventory_to', $to_id) }}
                    <i class="dropdown icon"></i>

                    <div class="default text">@lang('misc.pleaseSpecify')</div>
                    <div class="menu">
                        @foreach($economy->inventories as $i)
                            <div class="item" data-value="{{ $i->id }}">{{ $i->name }}</div>
                        @endforeach
                    </div>
                </div>

                {{ ErrorRenderer::inline('inventory_to') }}
            </div>
        </div>

        <div class="ui divider hidden"></div>

        {{-- Product list --}}
        <table class="ui celled table unstackable">
            <thead>
                <tr>
                    <th>@lang('pages.products.title') ({{ count($products) }})</th>
                    <th>@lang('misc.quantity')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                    <tr>
                        <td data-label="@lang('misc.product')">
                            <a href="{{ route('community.economy.product.show', [
                                        // TODO: this is not efficient
                                        'communityId' => $p['product']->economy->community->human_id,
                                        'economyId' => $p['product']->economy_id,
                                        'productId' => $p['product']->id,
                                    ]) }}" target="_blank">
                                {{ $p['product']->displayName() }}
                            </a>
                        </td>
                        <td data-label="@lang('misc.quantity')" class="right aligned collapsing">
                            <div class="field inventory-balance-quantity-field {{ ErrorRenderer::hasError($p['field'] . '_quantity') ? 'error' : '' }}">
                                {{ Form::text($p['field'] . '_quantity',
                                    is_checked(request()->query('all') ?? false) ? ($p['quantity'] ?? '') : '',
                                    [
                                        'placeholder' => $p['quantity'],
                                    ]) }}
                                {{-- Flush error for this field, inline rendering is bad --}}
                                {{ ErrorRenderer::consume($p['field'] . '_quantity') }}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2">@lang('pages.products.noProducts')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Exhausted product list --}}
        @if($exhaustedProducts->isNotEmpty())
            <div class="ui fluid accordion">
                <div class="title">
                    <i class="dropdown icon"></i>
                    @lang('pages.inventories.exhaustedProducts') ({{ count($exhaustedProducts) }})
                </div>
                <div class="content">
                    <table class="ui celled table unstackable">
                        <thead>
                            <tr>
                                <th>@lang('pages.inventories.exhaustedProducts') ({{ count($exhaustedProducts) }})</th>
                                <th>@lang('misc.quantity')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exhaustedProducts as $p)
                                <tr>
                                    <td data-label="@lang('misc.product')">
                                        <a href="{{ route('community.economy.product.show', [
                                                    // TODO: this is not efficient
                                                    'communityId' => $p['product']->economy->community->human_id,
                                                    'economyId' => $p['product']->economy_id,
                                                    'productId' => $p['product']->id,
                                                ]) }}" target="_blank">
                                            {{ $p['product']->displayName() }}
                                        </a>
                                    </td>
                                    <td data-label="@lang('misc.quantity')" class="right aligned collapsing">
                                        <div class="field inventory-balance-quantity-field {{ ErrorRenderer::hasError($p['field'] . '_quantity') ? 'error' : '' }}">
                                            {{ Form::text($p['field'] . '_quantity', '', [
                                                'placeholder' => $p['quantity'],
                                            ]) }}
                                            {{-- Flush error for this field, inline rendering is bad --}}
                                            {{ ErrorRenderer::consume($p['field'] . '_quantity') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="ui divider hidden"></div>

        <div class="required field {{ ErrorRenderer::hasError('comment') ? 'error' : '' }}">
            {{ Form::label('comment', __('misc.comment') . ':') }}
            {{ Form::text('comment', __('pages.inventories.defaultMoveComment')) }}
            {{ ErrorRenderer::inline('comment') }}
        </div>

        <div class="required field {{ ErrorRenderer::hasError('confirm') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('confirm', 1, false, [
                    'tabindex' => 0,
                    'class' => 'hidden',
                ]) }}
                {{ Form::label('confirm', __('pages.inventories.confirmChangeQuantities')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('confirm') }}
        </div>

        <div class="ui divider hidden"></div>

        <button class="ui button primary" type="submit">@lang('pages.inventories.move')</button>
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
