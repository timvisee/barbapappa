@extends('layouts.app')

@section('title', __('pages.bar.purchases'))
@php
    $breadcrumbs = Breadcrumbs::generate('bar.history', $bar);
    $menusection = 'bar_manage';

    use \App\Http\Controllers\BarController;

    const SEPARATE_DELAY_SECONDS = BarController::SUMMARY_SEPARATE_DELAY_SECONDS;
@endphp

@section('content')
    <h2 class="ui header bar-header">
        @yield('title')
    </h2>

    <p>@lang('pages.bar.purchasesDescription')</p>

    <div class="ui hidden divider"></div>

    <div class="ui top vertical menu fluid">
        @forelse($productMutations as $productMutation)
            @php
                $self = barauth()->getUser()->id == $productMutation->mutation->owner_id;

                // Show divider if delay to previous reaches threshold
                $showDivider = false;
                if(isset($previous)) {
                    $delay = $previous->created_at->diffAsCarbonInterval($productMutation->created_at);
                    if($delay->total('seconds') >= SEPARATE_DELAY_SECONDS)
                        $showDivider = true;
                }
            @endphp

            @if($showDivider)
                </div>
                <span class="glyphicons glyphicons-option-vertical chunk-spacer"></span>
                <div class="ui top vertical menu fluid">
            @endif
            @if($showDivider || $loop->first)
                <h5 class="ui item header">
                    @include('includes.humanTimeDiff', [
                        'time' => $productMutation->updated_at ?? $productMutation->created_at,
                        'absolute' => false,
                        'short' => false,
                        'ucfirst' => true,
                    ])

                    {{-- Relative delay --}}
                    @if(isset($delay))
                        <span class="subtle">
                            &nbsp;&middot;&nbsp; {{ $delay->forHumans(null, false, 1) }} @lang('misc.earlier')
                        </span>
                    @endif
                </h5>
            @endif

            @if($loop->first && !empty($productMutations->previousPageUrl()))
                <a class="item text-align-center" href="{{ $productMutations->previousPageUrl() }}">
                    <span class="glyphicons glyphicons-more list-more subtle"></span>
                </a>
            @endif

            <a class="item"
                href="{{ route('transaction.show', [
                    'transactionId' => $productMutation->mutation->transaction_id,
                ]) }}">

                @if($productMutation->quantity != 1)
                    <span class="subtle">{{ $productMutation->quantity }}×</span>
                @endif

                {{ ($product = $productMutation->product) ? $product->displayName() : __('pages.products.unknownProduct') }}
                {!! $productMutation->mutation->formatAmount(BALANCE_FORMAT_LABEL, [
                    'color' => $self,
                ]) !!}

                @if($productMutation->mutation->owner_id)
                    <span class="subtle">
                        &middot;&nbsp;{{ $productMutation->mutation->owner->first_name }}
                    </span>
                @endif

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

            @if($loop->last && !empty($productMutations->nextPageUrl()))
                <a class="item text-align-center" href="{{ $productMutations->nextPageUrl() }}">
                    <span class="glyphicons glyphicons-more list-more subtle"></span>
                </a>
            @endif

            @php
                $previous = $productMutation;
            @endphp
        @empty
            <i class="item">@lang('pages.bar.noPurchases')...</i>
        @endforelse
    </div>

    {{ $productMutations->links() }}

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
