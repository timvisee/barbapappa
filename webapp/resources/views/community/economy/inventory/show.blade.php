@extends('layouts.app')

@section('title', $inventory->name)
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.inventory.show', $inventory);
    $menusection = 'community_manage';

    use App\Http\Controllers\InventoryController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    @if(perms(InventoryController::permsManage()))
        <p>
            <div class="ui buttons">
                <a href="{{ route('community.economy.inventory.addRemove', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                        ]) }}"
                        class="ui button green">
                    @lang('pages.inventories.addRemove')
                </a>
                <a href="{{ route('community.economy.inventory.balance', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                        ]) }}"
                        class="ui button orange">
                    @lang('pages.inventories.rebalance')
                </a>
                <a href="{{ route('community.economy.inventory.move', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                        ]) }}"
                        class="ui button blue">
                    @lang('pages.inventories.move')
                </a>
            </div>
        </p>
    @endif

    {{-- Product list --}}
    <div class="ui vertical menu fluid">
        <h5 class="ui item header">
            @lang('pages.products.title') ({{ count($products) }})
        </h5>

        @forelse($products as $p)
            <a class="item"
                    href="{{ route('community.economy.inventory.product.show', [
                        // TODO: this is not efficient
                        'communityId' => $p['product']->economy->community->human_id,
                        'economyId' => $p['product']->economy_id,
                        'inventoryId' => $inventory->id,
                        'productId' => $p['product']->id,
                    ]) }}">
                {{ $p['product']->displayName() }}

                <div class="ui {{ $p['quantity'] < 0 ? 'red' : ($p['quantity'] > 0 ? 'green' : '') }} label">
                    {{ $p['quantity'] }}
                </div>

                @if($p['item'] != null)
                    <span class="sub-label">
                        @include('includes.humanTimeDiff', ['time' => $p['item']->updated_at ?? $p['item']->created_at])
                    </span>
                @endif
            </a>
        @empty
            <i class="item">@lang('pages.products.noProducts')</i>
        @endforelse
    </div>

    {{-- Exhausted product list --}}
    @if($exhaustedProducts->isNotEmpty())
        <div class="ui vertical menu fluid">
            <h5 class="ui item header">
                @lang('pages.inventories.exhaustedProducts') ({{ count($exhaustedProducts) }})
            </h5>

            @foreach($exhaustedProducts as $p)
                <a class="item"
                        href="{{ route('community.economy.inventory.product.show', [
                            // TODO: this is not efficient
                            'communityId' => $p['product']->economy->community->human_id,
                            'economyId' => $p['product']->economy_id,
                            'inventoryId' => $inventory->id,
                            'productId' => $p['product']->id,
                        ]) }}">
                    {{ $p['product']->displayName() }}

                    @if($p['item'] != null)
                        <span class="sub-label">
                            @include('includes.humanTimeDiff', ['time' => $p['item']->updated_at ?? $p['item']->created_at])
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif

    <div class="ui divider hidden"></div>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>{{ $inventory->name }}</td>
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
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $inventory->created_at])</td>
            </tr>
            @if($inventory->created_at != $inventory->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $inventory->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(perms(InventoryController::permsManage()))
        <p>
            <div class="ui buttons">
                <a href="{{ route('community.economy.inventory.edit', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                        ]) }}"
                        class="ui button secondary">
                    @lang('misc.edit')
                </a>
                <a href="{{ route('community.economy.inventory.delete', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                        ]) }}"
                        class="ui button negative">
                    @lang('misc.delete')
                </a>
            </div>
        </p>
    @endif

    <p>
        <a href="{{ route('community.economy.inventory.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
