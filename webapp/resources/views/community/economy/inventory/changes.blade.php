@extends('layouts.app')

@section('title', __('pages.inventories.allChanges'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.inventory.show', $inventory);
    $menusection = 'community_manage';

    use App\Http\Controllers\InventoryController;
    use App\Models\InventoryItemChange;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui fluid accordion">
        <div class="title">
            <i class="dropdown icon"></i>
            @lang('misc.filters')
        </div>
        <div class="content">
            {!! Form::open([
                'method' => 'GET',
                'class' => 'ui form'
            ]) !!}
                @foreach(InventoryItemChange::TYPES as $type)
                    <div class="inline field">
                        <div class="ui toggle checkbox">
                            {{ Form::checkbox('filter_' . $type, 0, !is_checked(request()->query('filter_' . $type) ?? true), ['tabindex' => 0, 'class' => 'hidden']) }}
                            {{ Form::label('filter_' . $type, __('pages.inventories.hideType') . ': ' . __('pages.inventories.type.' . $type)) }}
                        </div>
                    </div>
                @endforeach

                <button class="ui button primary" type="submit">@lang('misc.update')</button>

                <div class="ui divider hidden"></div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class="ui vertical menu fluid">
        @forelse($changes as $c)
            @if(($product = $c->item?->product) != null)
                <a class="item"
                        href="{{ route('community.economy.inventory.product.change', [
                            // TODO: this is not efficient
                            'communityId' => $product->economy->community->human_id,
                            'economyId' => $product->economy_id,
                            'inventoryId' => $inventory->id,
                            'productId' => $product->id,
                            'changeId' => $c->id,
                        ]) }}">
                    @if(!empty($c->comment))
                        <i>{{ $c->comment }}</i> ({{ $product->name }})
                    @else
                        @lang('pages.inventories.type.' . $c->type)
                        ({{ $product->name }})
                    @endif

                    @if($c->user)
                        <span class="subtle">&middot;&nbsp;{{ $c->user->first_name }}</span>
                    @endif

                    {!! $c->formatQuantity(InventoryItemChange::FORMAT_LABEL) !!}

                    <span class="sub-label">
                        @include('includes.humanTimeDiff', ['time' => $c->created_at])
                    </span>
                </a>
            @else
                <div class="item disabled">
                    @if(!empty($c->comment))
                        <i>{{ $c->comment }}</i>
                    @else
                        @lang('pages.inventories.type.' . $c->type)
                    @endif

                    @if($c->user)
                        <span class="subtle">&middot;&nbsp;{{ $c->user->first_name }}</span>
                    @endif

                    {!! $c->formatQuantity(InventoryItemChange::FORMAT_LABEL) !!}

                    <span class="sub-label">
                        @include('includes.humanTimeDiff', ['time' => $c->created_at])
                    </span>
                </div>
            @endif
        @empty
            <i class="item">@lang('pages.inventories.noChanges')...</i>
        @endforelse
    </div>

    {{ method_exists($changes, 'links') ? $changes->links() : '' }}

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
