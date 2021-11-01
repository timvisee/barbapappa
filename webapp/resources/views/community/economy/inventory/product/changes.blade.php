@extends('layouts.app')

@section('title', __('pages.inventories.allChanges'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.inventory.product.show', $inventory, $product);
    $menusection = 'community_manage';

    use App\Http\Controllers\InventoryController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui vertical menu fluid">
        @forelse($changes as $c)
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
    </div>

    {{ method_exists($changes, 'links') ? $changes->links() : '' }}

    <p>
        <a href="{{ route('community.economy.inventory.product.show', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'inventoryId' => $inventory->id,
            'productId' => $product->id,
        ]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
