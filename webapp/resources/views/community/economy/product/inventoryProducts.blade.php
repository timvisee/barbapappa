@extends('layouts.app')

@section('title', $product->name . ': ' . __('pages.products.inventoryProducts'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>
        @lang('pages.products.inventoryProductsDescription')
    </p>

    {!! Form::open([
        'action' => [
            'ProductController@doAddInventoryProduct',
            $community->human_id,
            $economy->id,
            $product->id,
        ],
        'method' => 'POST',
        'class' => 'ui form'
    ]) !!}
        <div class="field {{ ErrorRenderer::hasError('product') ? 'error' : '' }}">
            {{ Form::label('product', __('pages.products.addProduct') . ':') }}

            <div class="ui action input">
                {{ Form::select('product', $addProducts
                    ->map(function($p) {
                        return [$p->id => $p->displayName()];
                    }),
                    $product->id,
                    [
                        'class' => 'ui fluid search dropdown',
                        'placeholder' => __('misc.pleaseSpecify'),
                    ]) }}
                <button class="ui button positive" type="submit">@lang('misc.add')</button>
            </div>

            {{ ErrorRenderer::inline('product') }}
        </div>
    {!! Form::close() !!}

    <div class="ui divider hidden"></div>

    {!! Form::open([
        'action' => [
            'ProductController@doEditInventoryProducts',
            $community->human_id,
            $economy->id,
            $product->id,
        ],
        'method' => 'PUT',
        'class' => 'ui form'
    ]) !!}
        {{-- Hidden submit button, as default action when we press enter --}}
        <button type="submit" style="display: none;"></button>

        {{-- Product list --}}
        <table class="ui celled table unstackable">
            <thead>
                <tr>
                    <th>@lang('pages.products.title') ({{ count($products) }})</th>
                    <th>@lang('misc.quantity')</th>
                    <th>@lang('misc.actions')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                    @php
                        $field = 'product_' . $p->inventory_product_id . '_quantity';
                    @endphp

                    <tr>
                        <td data-label="@lang('misc.product')">
                            <a href="{{ route('community.economy.product.show', [
                                        // TODO: this is not efficient
                                        'communityId' => $p->inventoryProduct->economy->community->human_id,
                                        'economyId' => $p->inventoryProduct->economy_id,
                                        'productId' => $p->inventory_product_id,
                                    ]) }}" target="_blank">
                                {{ $p->inventoryProduct->displayName() }}
                            </a>
                        </td>
                        <td data-label="@lang('misc.quantity')" class="right aligned collapsing">
                            <div class="field inventory-balance-quantity-field {{ ErrorRenderer::hasError($field) ? 'error' : '' }}">
                                {{ Form::text($field, $p->quantity, [
                                    'placeholder' => 1,
                                    'inputmode' => 'numeric',
                                ]) }}
                                {{-- Flush error for this field, inline rendering is bad --}}
                                {{ ErrorRenderer::consume($field) }}
                            </div>
                        </td>
                        <td data-label="@lang('misc.delta')" class="right aligned collapsing">
                            <button type="submit"
                                    name="remove"
                                    value="{{ $p->inventory_product_id }}"
                                    class="ui basic negative button icon"
                                    style="padding: 0.425em;">
                                <span class="glyphicons glyphicons-remove"></span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">@lang('pages.products.noProducts')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="ui divider hidden"></div>

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('community.economy.product.edit', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'productId' => $product->id,
        ]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>

    {!! Form::close() !!}
@endsection
