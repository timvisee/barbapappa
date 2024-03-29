@extends('layouts.app')

@section('title', __('pages.inventories.type.' . $change->type))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.inventory.product.show', $inventory, $product);
    $menusection = 'community_manage';

    use App\Http\Controllers\InventoryController;
    use App\Models\InventoryItemChange;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                {!! $change->formatQuantity(InventoryItemChange::FORMAT_COLOR) !!}
            </div>
            <div class="label">@lang('misc.amount')</div>
        </div>
    </div>

    <div class="ui divider hidden"></div>

    @if(!empty($change->comment))
        <p class="align-center" title="@lang('misc.comment')">
            <i>{{ $change->comment }}</i>
            @if($change->user)
                <br>
                @lang('misc.by')
                <i>{{ $change->user->name }}</i>
            @endif
        </p>
        <div class="ui divider hidden"></div>
    @endif

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.type')</td>
                <td>@lang('pages.inventories.type.' . $change->type)</td>
            </tr>
            @if($change->type == InventoryItemChange::TYPE_MOVE && $change->related)
                <tr>
                    <td>@lang('pages.inventories.title')</td>
                    <td>
                        @if($change->quantity < 0)
                            <a href="{{ route('community.economy.inventory.show', [
                                    'communityId' => $product->economy->community->human_id,
                                    'economyId' => $product->economy_id,
                                    'inventoryId' => $inventory->id,
                                ]) }}">
                                {{ $inventory->name }}
                            </a>
                        @else
                            <a href="{{ route('community.economy.inventory.show', [
                                    'communityId' => $product->economy->community->human_id,
                                    'economyId' => $product->economy_id,
                                    'inventoryId' => $change->related->item->inventory_id,
                                ]) }}">
                                {{ $change->related->item->inventory->name }}
                            </a>
                        @endif
                        →
                        @if($change->quantity < 0)
                            <a href="{{ route('community.economy.inventory.show', [
                                    'communityId' => $product->economy->community->human_id,
                                    'economyId' => $product->economy_id,
                                    'inventoryId' => $change->related->item->inventory_id,
                                ]) }}">
                                {{ $change->related->item->inventory->name }}
                            </a>
                        @else
                            <a href="{{ route('community.economy.inventory.show', [
                                    'communityId' => $product->economy->community->human_id,
                                    'economyId' => $product->economy_id,
                                    'inventoryId' => $inventory->id,
                                ]) }}">
                                {{ $inventory->name }}
                            </a>
                        @endif
                    </td>
                </tr>
            @endif
            @if($change->user)
                <tr>
                    <td>@lang('misc.fromUser')</td>
                    <td>{{ $change->user->name }}</td>
                </tr>
            @endif
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
                <td>@lang('misc.firstSeen')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $item->updated_at])</td>
            </tr>
        </tbody>
    </table>

    <p>
        @if($change->related)
            <a class="ui basic button"
               href="{{ route('community.economy.inventory.product.change', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'inventoryId' => $change->related->item->inventory_id,
                    'productId' => $product->id,
                    'changeId' => $change->related_id,
               ]) }}">
               @lang('pages.inventories.viewRelated')
            </a>
        @endif

        @if($change->mutation_product)
            <a class="ui basic button"
               href="{{ route('transaction.mutation.show', [
                   'transactionId' => $change->mutation_product->mutation->transaction_id,
                   'mutationId' => $change->mutation_product->mutation->id,
               ]) }}">
               @lang('pages.mutations.viewMutation')
            </a>
        @endif

        @if($change->canUndo())
            <a href="{{ route('community.economy.inventory.product.change.undo', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id,
                    'inventoryId' => $change->item->inventory_id,
                    'productId' => $product->id,
                    'changeId' => $change->id,
                    ]) }}"
                    class="ui button basic">
                @lang('misc.undo')
            </a>
        @endif
    </p>

    <p>
        <a href="{{ route('community.economy.inventory.product.changes', [
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
