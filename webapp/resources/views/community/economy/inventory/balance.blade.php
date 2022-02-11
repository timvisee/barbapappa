@extends('layouts.app')

@section('title', __('pages.inventories.rebalanceProducts'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.inventory.show', $inventory);
    $menusection = 'community_manage';

    use App\Models\InventoryItemChange;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.inventories.rebalanceDescription')</p>

    <div class="ui divider hidden"></div>

    {!! Form::open([
        'action' => [
            'InventoryController@doBalance',
            $community->human_id,
            $economy->id,
            $inventory->id,
        ],
        'method' => 'PUT',
        'class' => 'ui form'
    ]) !!}

        {{-- Product list --}}
        <table class="ui celled table unstackable">
            <thead>
                <tr>
                    <th>@lang('pages.products.title') ({{ count($products) }})</th>
                    <th>@lang('misc.quantity')</th>
                    <th>@lang('misc.delta')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                    <tr>
                        <td data-label="@lang('misc.product')">
                            @if($p['quantity'] < 0)
                                <span class="halflings halflings-exclamation-sign"></span>
                            @endif

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
                            <div class="field inventory-balance-quantity-field {{ ErrorRenderer::hasError($p['field'] . '_quantity') ? 'error' : '' }}{{ $p['quantity'] < 0 ? ' error-outline' : '' }}">
                                {{ Form::text($p['field'] . '_quantity', '', [
                                    'placeholder' => $p['quantity'],
                                    'inputmode' => 'numeric',
                                ]) }}
                                {{-- Flush error for this field, inline rendering is bad --}}
                                {{ ErrorRenderer::consume($p['field'] . '_quantity') }}
                            </div>
                        </td>
                        <td data-label="@lang('misc.delta')" class="right aligned collapsing">
                            <div class="field inventory-balance-quantity-field {{ ErrorRenderer::hasError($p['field'] . '_delta') ? 'error' : '' }}">
                                {{ Form::text($p['field'] . '_delta', '', [
                                    'placeholder' => 0,
                                    'inputmode' => 'numeric',
                                ]) }}
                                {{-- Flush error for this field, inline rendering is bad --}}
                                {{ ErrorRenderer::consume($p['field'] . '_delta') }}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">@lang('pages.products.noProducts')</td>
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
                                <th>@lang('misc.delta')</th>
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
                                                'inputmode' => 'numeric',
                                            ]) }}
                                            {{-- Flush error for this field, inline rendering is bad --}}
                                            {{ ErrorRenderer::consume($p['field'] . '_quantity') }}
                                        </div>
                                    </td>
                                    <td data-label="@lang('misc.delta')" class="right aligned collapsing">
                                        <div class="field inventory-balance-quantity-field {{ ErrorRenderer::hasError($p['field'] . '_delta') ? 'error' : '' }}">
                                            {{ Form::text($p['field'] . '_delta', '', [
                                                'placeholder' => 0,
                                                'inputmode' => 'numeric',
                                            ]) }}
                                            {{-- Flush error for this field, inline rendering is bad --}}
                                            {{ ErrorRenderer::consume($p['field'] . '_delta') }}
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
            {{ Form::text('comment', __('pages.inventories.defaultRebalanceComment')) }}
            {{ ErrorRenderer::inline('comment') }}
        </div>

        <div class="required field {{ ErrorRenderer::hasError('type') ? 'error' : '' }}">
            {{ Form::label('type', __('pages.inventories.changeType')) }}

            <div class="ui fluid selection dropdown">
                {{ Form::hidden('type', InventoryItemChange::TYPE_BALANCE) }}
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.pleaseSpecify')</div>
                <div class="menu">
                    <div class="item" data-value="{{ InventoryItemChange::TYPE_BALANCE }}">
                        @lang('pages.inventories.type.' . InventoryItemChange::TYPE_BALANCE)
                        (@lang('general.recommended'))
                    </div>
                    <div class="item" data-value="{{ InventoryItemChange::TYPE_ADD_REMOVE }}">
                        @lang('pages.inventories.type.' . InventoryItemChange::TYPE_ADD_REMOVE)
                    </div>
                    <div class="item" data-value="{{ InventoryItemChange::TYPE_SET }}">
                        @lang('pages.inventories.type.' . InventoryItemChange::TYPE_SET)
                    </div>
                </div>
            </div>

            {{ ErrorRenderer::inline('type') }}
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

        <button class="ui button primary" type="submit">@lang('pages.inventories.rebalance')</button>
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
