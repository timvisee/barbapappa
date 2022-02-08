@extends('layouts.app')

@section('title', __('pages.inventories.undoChange'))
@php
    use App\Models\InventoryItemChange;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.inventories.undoChangeQuestion')</p>

    {!! Form::open(['action' => ['InventoryProductController@doUndo',
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'inventoryId' => $change->item->inventory_id,
            'productId' => $product->id,
            'changeId' => $change->id,
        ], 'method' => 'DELETE', 'class' => 'ui form']) !!}

        <table class="ui compact celled definition table">
            <tbody>
                <tr>
                    <td>@lang('misc.description')</td>
                    <td>
                        @if(!empty($change->comment))
                            <i>{{ $change->comment }}</i>
                        @else
                            @lang('pages.inventories.type.' . $change->type)
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('misc.quantity')</td>
                    <td>{!! $change->formatQuantity(InventoryItemChange::FORMAT_COLOR) !!}</td>
                </tr>
                @if($change->user)
                    <tr>
                        <td>@lang('misc.fromUser')</td>
                        <td>{{ $change->user->name }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="ui divider hidden"></div>

        <div class="ui buttons">
            <a href="{{ route('community.economy.inventory.product.change', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'inventoryId' => $change->item->inventory_id,
                        'productId' => $product->id,
                        'changeId' => $change->id,
                    ]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesUndo')</button>
        </div>
    {!! Form::close() !!}
@endsection
