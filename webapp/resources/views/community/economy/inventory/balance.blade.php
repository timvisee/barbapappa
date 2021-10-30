@extends('layouts.app')

@section('title', __('pages.inventories.rebalanceProducts'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.inventory.show', $inventory);
    $menusection = 'community_manage';
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
                            <div class="field
                                inventory-balance-quantity-field {{ ErrorRenderer::hasError($p['field'] . '_quantity') ? 'error' : '' }}">
                                {{ Form::text($p['field'] . '_quantity', '', [
                                    'placeholder' => $p['quantity'],
                                ]) }}
                                {{ ErrorRenderer::inline($p['field'] . '_quantity') }}
                            </div>
                        </td>
                        <td data-label="@lang('misc.delta')" class="right aligned collapsing">
                            <div class="field inventory-balance-quantity-field {{ ErrorRenderer::hasError($p['field'] . '_delta') ? 'error' : '' }}">
                                {{ Form::text($p['field'] . '_delta', '', [
                                    'placeholder' => 0,
                                ]) }}
                                {{ ErrorRenderer::inline($p['field'] . '_delta') }}
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
                                <div class="field
                                    inventory-balance-quantity-field {{ ErrorRenderer::hasError($p['field'] . '_quantity') ? 'error' : '' }}">
                                    {{ Form::text($p['field'] . '_quantity', '', [
                                        'placeholder' => $p['quantity'],
                                    ]) }}
                                    {{ ErrorRenderer::inline($p['field'] . '_quantity') }}
                                </div>
                            </td>
                            <td data-label="@lang('misc.delta')" class="right aligned collapsing">
                                <div class="field inventory-balance-quantity-field {{ ErrorRenderer::hasError($p['field'] . '_delta') ? 'error' : '' }}">
                                    {{ Form::text($p['field'] . '_delta', '', [
                                        'placeholder' => 0,
                                    ]) }}
                                    {{ ErrorRenderer::inline($p['field'] . '_delta') }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="ui divider hidden"></div>

        <div class="required field {{ ErrorRenderer::hasError('comment') ? 'error' : '' }}">
            {{ Form::label('comment', __('misc.comment') . ':') }}
            {{ Form::text('comment', __('pages.inventories.defaultRebalanceComment')) }}
            {{ ErrorRenderer::inline('comment') }}
        </div>

        <div class="required field {{ ErrorRenderer::hasError('confirm') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('confirm', 1, false, [
                    'tabindex' => 0,
                    'class' => 'hidden',
                ]) }}
                {{ Form::label('confirm', __('pages.inventories.confirmBalanceComplete')) }}
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
