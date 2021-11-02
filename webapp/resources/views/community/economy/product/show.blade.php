@extends('layouts.app')

@section('title', $product->name)
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.product.show', $product);
    $menusection = 'community_manage';

    use App\Http\Controllers\ProductController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>
                    <div class="ui list">
                        <div class="item">{{ $product->name }}</div>
                        @foreach($product->names as $name)
                            <div class="item">
                                <i>{{ $name->languageName() }}:</i>
                                {{ $name->name }}
                            </div>
                        @endforeach
                    </div>
                </td>
            </tr>
            @if($product->tags)
                <tr>
                    <td>@lang('misc.tags') (@lang('misc.search'))</td>
                    <td>
                        <div class="ui list">
                            <div class="item">{{ $product->tags }}</div>
                        </div>
                    </td>
                </tr>
            @endif
            <tr>
                <td>@lang('pages.products.prices')</td>
                <td>
                    @if($product->prices->isNotEmpty())
                        <div class="ui list">
                            @foreach($product->prices as $price)
                                <div class="item">{{ $price->formatPrice() }}</div>
                            @endforeach
                        </div>
                    @else
                        <i class="ui text negative">@lang('misc.none')</i>
                    @endif
                </td>
            </tr>
            <tr>
                <td>@lang('misc.type')</td>
                <td>{{ $product->typeName() }}</td>
            </tr>
            <tr>
                <td>@lang('pages.community.economy')</td>
                <td>
                    <a href="{{ route('community.economy.show', [
                                'communityId'=> $community->human_id,
                                'economyId' => $economy->id
                            ]) }}">
                        {{ $economy->name }}
                    </a>
                </td>
            </tr>
            @if($product->user_id != null)
                <tr>
                    <td>@lang('misc.createdBy')</td>
                    <td>{{ $product->user->name }}</td>
                </tr>
            @endif
            <tr>
                <td>@lang('misc.trashed')</td>
                @if(!$product->trashed())
                    <td>{{ yesno(false) }}</td>
                @else
                    <td>
                        <span class="ui text negative">
                            @include('includes.humanTimeDiff', ['time' => $product->deleted_at])
                        </span>
                    </td>
                @endif
            </tr>
            @if($product->created_user)
                <tr>
                    <td>@lang('misc.createdBy')</td>
                    <td>{{ $product->created_user->name }}</td>
                </tr>
            @endif
            @if($product->updated_user)
                <tr>
                    <td>@lang('misc.lastUpdatedBy')</td>
                    <td>{{ $product->updated_user->name }}</td>
                </tr>
            @endif
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $product->created_at])</td>
            </tr>
            @if($product->created_at != $product->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $product->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(perms(ProductController::permsManage()))
        <p>
            <div class="ui buttons">
                @if(!$product->trashed())
                    <a href="{{ route('community.economy.product.edit', [
                                'communityId' => $community->human_id,
                                'economyId' => $economy->id,
                                'productId' => $product->id,
                            ]) }}"
                            class="ui button secondary">
                        @lang('misc.edit')
                    </a>
                @else
                    <a href="{{ route('community.economy.product.restore', [
                                'communityId' => $community->human_id,
                                'economyId' => $economy->id,
                                'productId' => $product->id,
                            ]) }}"
                            class="ui button primary">
                        @lang('misc.restore')
                    </a>
                @endif
                <a href="{{ route('community.economy.product.create', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'productId' => $product->id,
                        ]) }}"
                        class="ui button positive">
                    @lang('misc.clone')
                </a>
                <a href="{{ route('community.economy.product.delete', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'productId' => $product->id,
                        ]) }}"
                        class="ui button negative">
                    @lang('misc.delete')
                </a>
            </div>
        </p>
    @endif

    {{-- Inventory quantities --}}
    @if($quantities->isNotEmpty())
        <div class="ui divider hidden"></div>

        <div class="ui vertical menu fluid">
            <h5 class="ui item header">
                @lang('pages.inventories.inventoryQuantities')
            </h5>

            @foreach($quantities as $q)
                <a class="item"
                        href="{{ route('community.economy.inventory.product.show', [
                            // TODO: this is not efficient
                            'communityId' => $product->economy->community->human_id,
                            'economyId' => $product->economy_id,
                            'inventoryId' => $q['inventory']->id,
                            'productId' => $product->id,
                        ]) }}">
                    {{ $q['inventory']->name }}

                    <div class="ui {{ $q['quantity'] < 0 ? 'red' : ($q['quantity'] > 0 ? 'green' : '') }} label">
                        {{ $q['quantity'] }}
                    </div>

                    @if(isset($q['item']) && $q['item'] != null)
                        <span class="sub-label">
                            @include('includes.humanTimeDiff', ['time' => $q['item']->updated_at])
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif

    <p>
        <a href="{{ route('community.economy.product.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
