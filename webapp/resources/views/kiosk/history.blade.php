@extends('layouts.app')

@section('title', __('pages.bar.purchases'))

@push('styles')
    <style>
        .center {
            text-align: center;
        }

        /* TODO: a hack to center toolbar logo, fix this */
        .toolbar-logo {
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
        }
    </style>
@endpush

@section('content')

    <div class="center">
        <a href="{{ route('kiosk.main') }}"
                    class="ui big basic button center aligned">
            @lang('pages.kiosk.backToKiosk')
        </a>
    </div>

    <h2 class="ui header center aligned">@yield('title')</h2>

    {{-- Recently bought products list --}}
    <div class="ui top vertical huge menu fluid">
        <h5 class="ui item header">
            {{ trans_choice('pages.products.recentlyBoughtProducts#', $productMutations->sum('quantity')) }}
        </h5>

        @forelse($productMutations as $productMutation)
            <div class="item">
                @if($productMutation->quantity != 1)
                    <span class="subtle">{{ $productMutation->quantity }}×</span>
                @endif

                {{ ($product = $productMutation->product) ?  $product->displayName() : __('pages.products.unknownProduct') }}
                {!! $productMutation->mutation->formatAmount(BALANCE_FORMAT_LABEL) !!}

                @if($productMutation->mutation->owner_id != null)
                    <span class="subtle">
                        @lang('misc.by') {{ $productMutation->mutation->owner->first_name }}
                    </span>
                @endif

                <span class="sub-label">
                    @include('includes.humanTimeDiff', ['time' => $productMutation->updated_at ?? $productMutation->created_at, 'short' => true])
                </span>
            </div>
        @empty
            <i class="item">@lang('pages.bar.noPurchases')...</i>
        @endforelse
    </div>
@endsection
