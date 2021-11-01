@extends('layouts.app')

@section('title', $inventory->name . ': ' . $product->displayName())
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.inventory.product.show', $inventory, $product);
    $menusection = 'community_manage';

    use App\Http\Controllers\InventoryController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                @if($item != null && $item->quantity > 0)
                    <span class="ui text positive">{{ $item->quantity }}</span>
                @elseif($item != null && $item->quantity < 0)
                    <span class="ui text negative">{{ $item->quantity }}</span>
                @else
                    0
                @endif
            </div>
            <div class="label">@lang('misc.quantity')</div>
        </div>
    </div>
    <br>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('pages.inventories.inventory')</td>
                <td>
                    <a href="{{ route('community.economy.inventory.show', [
                            'communityId' => $product->economy->community->human_id,
                            'economyId' => $product->economy_id,
                            'inventoryId' => $inventory->id,
                        ]) }}">
                        {{ $inventory->name }}
                    </a>
                </td>
            </tr>
            <tr>
                <td>@lang('misc.product')</td>
                <td>
                    <a href="{{ route('community.economy.product.show', [
                            'communityId' => $product->economy->community->human_id,
                            'economyId' => $product->economy_id,
                            'productId' => $product->id,
                        ]) }}">
                        {{ $product->displayName() }}
                    </a>
                </td>
            </tr>
            <tr>
                <td>@lang('misc.lastChanged')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $inventory->updated_at])</td>
            </tr>
            <tr>
                <td>@lang('pages.inventories.lastBalanced')</td>
                @if($lastBalanced != null)
                    <td>@include('includes.humanTimeDiff', ['time' => $lastBalanced])</td>
                @else
                    <td><span class="ui text negative">@lang('misc.never')</span></td>
                @endif
            </tr>
            <tr>
                <td>@lang('misc.trackedSince')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $inventory->created_at])</td>
            </tr>
        </tbody>
    </table>

    {{-- Inventory quantities --}}
    <div class="ui vertical menu fluid">
        <h5 class="ui item header">
            @lang('pages.inventories.inventoryQuantities')
        </h5>

        @foreach($quantities as $q)
            <a class="item {{ $inventory->id == $q['inventory']->id ? 'active' : '' }}"
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
            </a>
        @endforeach
    </div>

    {{-- Recent changes --}}
    <div class="ui vertical menu fluid">
        <h5 class="ui item header">
            {{ trans_choice('pages.inventories.last#Changes', $changes->count()) }}
        </h5>

        @forelse($changes as $c)
            {{-- TODO: use proper URL here --}}
            <a class="item"
                    href="{{ route('community.economy.inventory.product.show', [
                        // TODO: this is not efficient
                        'communityId' => $product->economy->community->human_id,
                        'economyId' => $product->economy_id,
                        'inventoryId' => $inventory->id,
                        'productId' => $product->id,
                    ]) }}">
                @if(!empty($c->comment))
                    <i>{{ $c->comment }}</i>
                @else
                    @lang('pages.inventories.type.' . $c->type)
                @endif

                <div class="ui {{ $c->quantity < 0 ? 'red' : ($c->quantity > 0 ? 'green' : '') }} label">
                    {{ $c->quantity }}
                </div>

                <span class="sub-label">
                    @include('includes.humanTimeDiff', ['time' => $c->created_at])
                </span>
            </a>
        @empty
            <i class="item">@lang('pages.inventories.noChanges')...</i>
        @endforelse
        <a href="{{ route('community.economy.inventory.product.changes', [
                    // TODO: this is not efficient
                    'communityId' => $product->economy->community->human_id,
                    'economyId' => $product->economy_id,
                    'inventoryId' => $inventory->id,
                    'productId' => $product->id,
                ]) }}"
                class="ui large bottom attached button">
            @lang('pages.inventories.allChanges')...
        </a>
    </div>

    <p>
        <a href="{{ route('community.economy.inventory.show', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'inventoryId' => $inventory->id,
        ]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
