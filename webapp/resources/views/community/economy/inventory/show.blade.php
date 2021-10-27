@extends('layouts.app')

@section('title', $inventory->name)
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.inventory.show', $inventory);
    $menusection = 'community_manage';

    use App\Http\Controllers\InventoryController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {{-- Product list --}}
    <div class="ui vertical menu fluid{{ !empty($class) ? ' ' . implode(' ', $class) : '' }}">
        <h5 class="ui item header">@lang('pages.products.title')</h5>

        @forelse($products as $product)
            @php
                // This is inefficient, improve this
                $item = $inventory->getItem($product);
                $quantity = $item != null ? $item->quantity : 0;
            @endphp

            @if($quantity != 0)
                <a class="item"
                        href="{{ route('community.economy.product.show', [
                            // TODO: this is not efficient
                            'communityId' => $product->economy->community->human_id,
                            'economyId' => $product->economy_id,
                            'productId' => $product->id,
                        ]) }}">
                    {{ $product->displayName() }}

                    <div class="ui {{ $quantity < 0 ? 'red' : ($quantity > 0 ? 'green' : '') }} label">
                        {{ $quantity }}
                    </div>

                    @if($item != null)
                        <span class="sub-label">
                            @include('includes.humanTimeDiff', ['time' => $item->updated_at ?? $item->created_at])
                        </span>
                    @endif
                </a>
            @endif
        @empty
            <i class="item">@lang('pages.products.noProducts')</i>
        @endforelse
    </div>

    {{-- Product list for 0 quantities --}}
    <div class="ui vertical menu fluid{{ !empty($class) ? ' ' . implode(' ', $class) : '' }}">
        <h5 class="ui item header">@lang('pages.inventories.exhaustedProducts')</h5>

        @foreach($products as $product)
            @php
                // This is inefficient, improve this
                $item = $inventory->getItem($product);
                $quantity = $item != null ? $item->quantity : 0;
            @endphp

            @if($quantity == 0)
                <a class="item"
                        href="{{ route('community.economy.product.show', [
                            // TODO: this is not efficient
                            'communityId' => $product->economy->community->human_id,
                            'economyId' => $product->economy_id,
                            'productId' => $product->id,
                        ]) }}">
                    {{ $product->displayName() }}

                    @if($item != null)
                        <span class="sub-label">
                            @include('includes.humanTimeDiff', ['time' => $item->updated_at ?? $item->created_at])
                        </span>
                    @endif
                </a>
            @endif
        @endforeach
    </div>

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
