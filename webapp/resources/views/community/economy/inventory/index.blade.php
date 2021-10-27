@extends('layouts.app')

@section('title', __('pages.inventories.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.inventory.index', $economy);
    $menusection = 'community_manage';

    use App\Http\Controllers\InventoryController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {{-- Inventory list --}}
    <div class="ui vertical menu fluid{{ !empty($class) ? ' ' . implode(' ', $class) : '' }}">
        <h5 class="ui item header">
            @yield('title') ({{ $inventories->count() }})
        </h5>

        @forelse($inventories as $inventory)
            <a class="item"
                    href="{{ route('community.economy.inventory.show', [
                        'communityId' => $inventory->economy->community->human_id,
                        'economyId' => $inventory->economy_id,
                        'inventoryId' => $inventory->id,
                    ]) }}">
                {{ $inventory->name }}

                <span class="sub-label">
                    @include('includes.humanTimeDiff', ['time' => $inventory->updated_at ?? $inventory->created_at])
                </span>
            </a>
        @empty
            <i class="item">@lang('pages.inventories.noInventories')</i>
        @endforelse
    </div>

    <p>
        @if(perms(InventoryController::permsManage()))
            <a href="{{ route('community.economy.inventory.create', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                    class="ui button basic positive">
                @lang('misc.create')
            </a>
        @endif

        <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('pages.economies.backToEconomy')
        </a>
    </p>
@endsection
