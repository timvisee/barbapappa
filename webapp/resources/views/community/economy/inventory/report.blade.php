@extends('layouts.app')

@section('title', $inventory->name . ': ' . __('pages.inventories.periodReport'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.inventory.show', $inventory);
    $menusection = 'community_manage';

    use App\Http\Controllers\InventoryController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {{-- Period input --}}
    {!! Form::open([
        'method' => 'GET',
        'class' => 'ui form'
    ]) !!}
        <div class="two fields">
            <div class="required field {{ ErrorRenderer::hasError('time_from') ? 'error' : '' }}">
                {{ Form::label('time_from', __('misc.fromTime') . ':') }}
                {{ Form::datetimeLocal('time_from', $timeFrom?->toDateTimeLocalString('minute'), [
                    'min' => $inventory->created_at->floorDay()->toDateTimeLocalString('minute'),
                    'max' => now()->toDateTimeLocalString('minute'),
                ]) }}
                {{ ErrorRenderer::inline('time_from') }}
            </div>

            <div class="required field {{ ErrorRenderer::hasError('time_to') ? 'error' : '' }}">
                {{ Form::label('time_to', __('misc.toTime') . ':') }}
                {{ Form::datetimeLocal('time_to', ($timeTo ?? now())->toDateTimeLocalString('minute'), [
                    'min' => $inventory->created_at->floorDay()->toDateTimeLocalString('minute'),
                    'max' => now()->toDateTimeLocalString('minute'),
                ]) }}
                {{ ErrorRenderer::inline('time_to') }}
            </div>
        </div>

        <button class="ui button blue" type="submit">@lang('misc.report')</button>

        <div class="ui buttons">
            @if(!$inventory->created_at->addWeek()->isFuture())
                <a href="{{ route('community.economy.inventory.report', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                            'time_from' => now()->subWeek()->max($inventory->created_at)->toDateTimeLocalString('minute'),
                            'time_to' => now()->toDateTimeLocalString('minute'),
                        ]) }}"
                        class="ui button">
                    @lang('pages.inventories.period.week')
                </a>
            @endif
            @if(!$inventory->created_at->addMonth()->isFuture())
                <a href="{{ route('community.economy.inventory.report', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                            'time_from' => now()->subMonth()->max($inventory->created_at)->toDateTimeLocalString('minute'),
                            'time_to' => now()->toDateTimeLocalString('minute'),
                        ]) }}"
                        class="ui button">
                    @lang('pages.inventories.period.month')
                </a>
            @endif
            @if(!$inventory->created_at->addYear()->isFuture())
                <a href="{{ route('community.economy.inventory.report', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id,
                            'inventoryId' => $inventory->id,
                            'time_from' => now()->subYear()->max($inventory->created_at)->toDateTimeLocalString('minute'),
                            'time_to' => now()->toDateTimeLocalString('minute'),
                        ]) }}"
                        class="ui button">
                    @lang('pages.inventories.period.year')
                </a>
            @endif
        </div>
    {!! Form::close() !!}

    <div class="ui divider hidden"></div>

    @if($timeFrom && $timeTo)
        <h5 class="ui header">
            @lang('pages.inventories.periodOfFromTo', [
                'period' => $timeFrom->longAbsoluteDiffForHumans($timeTo),
                'from' => $timeFrom->longRelativeDiffForHumans(),
                'to' => $timeTo->longRelativeDiffForHumans(),
            ]):
        </h5>

        @if(!isset($notBalanced) || $notBalanced)
            <div class="ui warning message">
                <span class="halflings halflings-warning-sign icon"></span>
                @lang('pages.inventories.warningNoBalanceChangesThisPeriod')
            </div>
        @endif

        <div class="ui divider hidden"></div>

        <div class="ui two small statistics">
            <div class="statistic">
                <div class="value">{{ $timeFrom->shortAbsoluteDiffForHumans($timeTo) }}</div>
                <div class="label">@lang('pages.inventories.stats.period')</div>
            </div>
            <div class="statistic">
                <div class="value">
                    {!! $stats['changeCount'][0] !!}
                </div>
                <div class="label">@lang('pages.inventories.stats.changeCount')</div>
            </div>
        </div>

        <div class="ui hidden divider"></div>

        <div class="ui two small statistics">
            <div class="statistic">
                <div class="value">
                    @if(isset($stats['unbalanceSum']))
                        {!! $stats['unbalanceSum'][0] !!}
                    @else
                        -
                    @endif
                </div>
                <div class="label">@lang('pages.inventories.stats.unbalanceSum')</div>
            </div>
            <div class="statistic">
                <div class="value">
                    @if(isset($stats['unbalanceMoney']))
                        {!! $stats['unbalanceMoney'][0] !!}
                    @else
                        -
                    @endif
                </div>
                <div class="label">@lang('pages.inventories.stats.unbalanceMoney')</div>
            </div>
        </div>

        <div class="ui divider hidden"></div>

        {{-- Unbalanced products --}}
        <div class="ui vertical menu fluid">
            <h5 class="ui item header">
                @lang('pages.inventories.unbalancedProducts') ({{ count($unbalanced) }})
            </h5>

            @forelse($unbalanced as $p)
                <a class="item"
                        href="{{ route('community.economy.inventory.product.show', [
                            // TODO: this is not efficient
                            'communityId' => $p['product']->economy->community->human_id,
                            'economyId' => $p['product']->economy_id,
                            'inventoryId' => $inventory->id,
                            'productId' => $p['product']->id,
                        ]) }}">
                    {{ $p['product']->displayName() }}

                    <div class="ui {{ $p['unbalance'] < 0 ? 'red' : ($p['unbalance'] > 0 ? 'green' : '') }} label">
                        @if($p['unbalance'] > 0)
                            +{{ $p['unbalance'] }}
                        @else
                            {{ $p['unbalance'] }}
                        @endif
                    </div>

                    @if(isset($p['unbalanceMoney']))
                        {!! $p['unbalanceMoney']->formatAmount(BALANCE_FORMAT_LABEL) !!}
                    @endif

                    <span class="sub-label">
                        {{ $p['balanceCount'] }}×,
                        {{ $p['unbalanceVolume'] }} @lang('pages.inventories.volumeShort'),
                        {{ $p['unbalancePercent'] }}%
                    </span>
                </a>
            @empty
                <i class="item">@lang('pages.products.noProducts')</i>
            @endforelse
        </div>

        {{-- Purchase volumes --}}
        @if(isset($purchaseVolumes) && $purchaseVolumes->isNotEmpty())
            <div class="ui vertical menu fluid">
                <h5 class="ui item header">
                    @lang('pages.inventories.purchaseVolumeByProduct') ({{ count($purchaseVolumes) }})
                </h5>

                @foreach($purchaseVolumes as $p)
                    <a class="item"
                            href="{{ route('community.economy.inventory.product.show', [
                                // TODO: this is not efficient
                                'communityId' => $p['product']->economy->community->human_id,
                                'economyId' => $p['product']->economy_id,
                                'inventoryId' => $inventory->id,
                                'productId' => $p['product']->id,
                            ]) }}">
                        {{ $p['product']->displayName() }}

                        <div class="ui blue label">
                            {{ $p['volume'] }}×
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="ui divider hidden"></div>

        {{-- Stats list --}}
        <table class="ui compact celled definition table">
            <tbody>
                @foreach($stats as $name => [$value, $note])
                    <tr>
                        <td>@lang('pages.inventories.stats.' . $name)</td>
                        <td>
                            {!! $value !!}
                            @if(!empty($note))
                                <span class="subtle">({!! $note !!})</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="ui divider hidden"></div>

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
