@extends('layouts.app')

@section('title', $inventory->name)
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.inventory.show', $inventory);
    $menusection = 'community_manage';

    use App\Http\Controllers\InventoryController;
    use App\Models\InventoryItemChange;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>
        @if(perms(InventoryController::permsManage()))
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
                        class="ui button teal">
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
        @endif
    </p>

    <p>
        @if(perms(InventoryController::permsView()))
            <div class="ui buttons">
                <a href="{{ route('community.economy.inventory.report', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                            'time_to' => $time?->toDateTimeLocalString('minute'),
                        ]) }}"
                        class="ui button violet">
                    @lang('pages.inventories.periodReport')
                </a>

                @unless(request()->query('time'))
                    <a href="{{ route('community.economy.inventory.show', [
                                'communityId' => $community->human_id,
                                'economyId' => $economy->id,
                                'inventoryId' => $inventory->id,
                                'time' => now()->toDateTimeLocalString('minute'),
                            ]) }}"
                            class="ui button purple">
                        @lang('pages.inventories.timeTravel')
                    </a>
                @endunless
            </div>
        @endif
    </p>

    {{-- Time travel field --}}
    @if(request()->query('time'))
        {!! Form::open([
            'method' => 'GET',
            'class' => 'ui form'
        ]) !!}
            <div class="field {{ ErrorRenderer::hasError('time') ? 'error' : '' }}">
                {{ Form::label('time', __('pages.inventories.travelToTime') . ':') }}

                <div class="ui action input">
                    {{ Form::datetimeLocal('time', $time->toDateTimeLocalString('minute'), [
                        'min' => $inventory->created_at->floorDay()->toDateTimeLocalString('minute'),
                        'max' => now()->ceilMinute()->toDateTimeLocalString('minute'),
                    ]) }}
                    <button class="ui button purple" type="submit">@lang('misc.go')!</button>
                </div>

                {{ ErrorRenderer::inline('time') }}
            </div>
        {!! Form::close() !!}

        <p>
            <div class="ui buttons">
                <a href="{{ route('community.economy.inventory.show', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                            'time' => $time->clone()->subWeek()->max($inventory->created_at->floorDay())->toDateTimeLocalString('minute'),
                        ]) }}"
                        class="ui labeled icon button">
                    <i class="icon glyphicons glyphicons-rewind"></i>
                    @lang('pages.inventories.period.week')
                </a>
                <a href="{{ route('community.economy.inventory.show', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                            'time' => $time->clone()->addWeek()->min(now())->toDateTimeLocalString('minute'),
                        ]) }}"
                        class="ui right labeled icon button">
                    <i class="right icon glyphicons glyphicons-forward"></i>
                    @lang('pages.inventories.period.week')
                </a>
            </div>
        </p>

        <p>
            <div class="ui buttons">
                <a href="{{ route('community.economy.inventory.show', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                            'time' => $time->clone()->subMonth()->max($inventory->created_at->floorDay())->toDateTimeLocalString('minute'),
                        ]) }}"
                        class="ui labeled icon button">
                    <i class="icon glyphicons glyphicons-rewind"></i>
                    @lang('pages.inventories.period.month')
                </a>
                <a href="{{ route('community.economy.inventory.show', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                            'time' => $time->clone()->addMonth()->min(now())->toDateTimeLocalString('minute'),
                        ]) }}"
                        class="ui right labeled icon button">
                    <i class="right icon glyphicons glyphicons-forward"></i>
                    @lang('pages.inventories.period.month')
                </a>
            </div>
        </p>

        <h5 class="ui header">
            @include('includes.humanTimeDiff', ['time' => $time]):
        </h5>
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

                @if(isset($p['changed']))
                    <span class="sub-label">
                        @include('includes.humanTimeDiff', [
                            'time' => $p['changed'],
                            'absolute' => true,
                            'short' => true,
                        ])
                    </span>
                @endif
            </a>
        @empty
            <i class="item">@lang('pages.products.noProducts')</i>
        @endforelse
    </div>

    {{-- Exhausted product list --}}
    @if($exhaustedProducts->isNotEmpty())
        <div class="ui fluid accordion">
            <div class="title">
                <i class="dropdown icon"></i>
                @lang('pages.inventories.exhaustedProducts') ({{ count($exhaustedProducts) }})
            </div>
            <div class="content">
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

                            @if(isset($p['changed']))
                                <span class="sub-label">
                                    @include('includes.humanTimeDiff', [
                                        'time' => $p['changed'],
                                        'absolute' => true,
                                        'short' => true,
                                    ])
                                </span>
                            @endif
                        </a>
                    @endforeach
                </div>
                <br />
            </div>
        </div>
    @endif

    {{-- Recent changes --}}
    <div class="ui fluid accordion">
        <div class="title">
            <i class="dropdown icon"></i>
            {{ trans_choice('pages.inventories.last#Changes', $changes->count()) }}
        </div>
        <div class="content">
            <div class="ui vertical menu fluid">
                <h5 class="ui item header">
                    {{ trans_choice('pages.inventories.last#Changes', $changes->count()) }}
                </h5>

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
                                @include('includes.humanTimeDiff', [
                                    'time' => $c->created_at,
                                    'absolute' => true,
                                    'short' => true,
                                ])
                            </span>
                        </a>
                    @else Details
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
                                @include('includes.humanTimeDiff', [
                                    'time' => $c->created_at,
                                    'absolute' => true,
                                    'short' => true,
                                ])
                            </span>
                        </div>
                    @endif
                @empty
                    <i class="item">@lang('pages.inventories.noChanges')...</i>
                @endforelse
                <a href="{{ route('community.economy.inventory.changes', [
                            // TODO: this is not efficient
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                        ]) }}"
                        class="ui large bottom attached button">
                    @lang('pages.inventories.allChanges')...
                </a>
            </div>
            <br>
        </div>
    </div>

    <div class="ui fluid accordion">
        <div class="title">
            <i class="dropdown icon"></i>
            @lang('misc.details')
        </div>
        <div class="content">
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
        </div>
    </div>

    <br />

    <p>
        <a href="{{ route('community.economy.inventory.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
