@extends('layouts.app')

@section('title', __('pages.bar.purchaseSummary'))
@php
    $breadcrumbs = Breadcrumbs::generate('bar.summary', $bar);
    $menusection = 'bar_manage';
@endphp

@section('content')
    <h2 class="ui header bar-header">
        @yield('title')
    </h2>

    <p>@lang('pages.bar.purchaseSummaryDescription')</p>

    <div class="ui hidden divider"></div>

    @forelse($users as $productMutations)
        <div class="ui top vertical menu fluid">

        <h5 class="ui item header">
            {{ $productMutations[0]->mutation->owner->name }}

            {{-- Relative delay --}}
            <span class="subtle">
                &nbsp;&middot;&nbsp;
                @php
                    $first = $productMutations->first()->updated_at ?? $productMutation->first()->created_at;
                    $last = $productMutations->last()->updated_at ?? $productMutation->last()->created_at;
                @endphp
                @include('includes.humanTimeDiff', [
                    'time' => $first,
                    'absolute' => true,
                    'short' => true,
                ])
                @if($first != $last)
                    -
                    @include('includes.humanTimeDiff', [
                        'time' => $last,
                        'absolute' => true,
                        'short' => true,
                    ])
                @endif
            </span>

            {!! $productMutations[0]->mutation->formatAmount(BALANCE_FORMAT_LABEL, [
                'color' => true,
            ]) !!}
        </h5>

        @foreach($productMutations as $productMutation)
            @php
                $self = barauth()->getUser()->id == $productMutation->mutation->owner_id;
            @endphp

            <a class="item"
                href="{{ route('transaction.show', [
                    'transactionId' => $productMutation->mutation->transaction_id,
                ]) }}">

                @if($productMutation->quantity != 1)
                    <span class="subtle">{{ $productMutation->quantity }}×</span>
                @endif

                {{ ($product = $productMutation->product) ?  $product->displayName() : __('pages.products.unknownProduct') }}
                {!! $productMutation->mutation->formatAmount(BALANCE_FORMAT_LABEL, [
                    'color' => $self,
                ]) !!}

                <span class="sub-label">
                    @include('includes.humanTimeDiff', [
                        'time' => $productMutation->updated_at ?? $productMutation->created_at,
                        'absolute' => true,
                        'short' => true,
                    ])

                    {{-- Icon for delayed purchases --}}
                    @if($productMutation->mutation?->transaction?->isDelayed() ?? false)
                        <span class="halflings halflings-hourglass"></span>
                    @endif

                    {{-- Icon for kiosk purchases --}}
                    @if($productMutation->mutation?->transaction?->initiated_by_kiosk ?? false)
                        <span class="halflings halflings-shopping-cart"></span>
                    @endif
                </span>
            </a>
        @endforeach

        </div>

    @empty
        <i class="item">@lang('pages.bar.noPurchases')...</i>
    @endforelse

    <p>
        <div class="ui floating right labeled icon dropdown button">
            <i class="dropdown icon"></i>
            @lang('misc.manage')
            <div class="menu">
                <a href="{{ route('bar.history.export', ['barId' => $bar->human_id]) }}"
                        class="item">
                    @lang('misc.export')
                </a>
            </div>
        </div>

        <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.backToBar')
        </a>
    </p>
@endsection
