@php
    use \App\Http\Controllers\BarController;
@endphp

{{-- Recently bought products list --}}
@if($productMutations->isNotEmpty())
    <div class="ui large top vertical menu fluid">
        <h5 class="ui item header">
            {{ trans_choice('pages.products.recentlyBoughtProducts#', $productMutations->sum('quantity')) }}
        </h5>

        @foreach($productMutations as $productMutation)
            @php
                $self = barauth()->getUser()->id == $productMutation->mutation->owner_id;
                $linkTransaction = $self || perms(BarController::permsManage());
                $linkProduct = $productMutation->product_id != null;
            @endphp

            @if($linkTransaction || $linkProduct)
                <a class="item"
                    href="{{ $linkTransaction ? route('transaction.show', [
                        'transactionId' => $productMutation->mutation->transaction_id,
                    ]) : route('bar.product.show', [
                        'barId' => $bar->human_id,
                        'productId' => $productMutation->product_id,
                    ])}}">
            @else
                <div class="item">
            @endif

                @if($productMutation->quantity != 1)
                    <span class="subtle">{{ $productMutation->quantity }}×</span>
                @endif

                {{ ($product = $productMutation->product) ?  $product->displayName() : __('pages.products.unknownProduct') }}
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

            @if($linkTransaction || $linkProduct)
                </a>
            @else
                </div>
            @endif
        @endforeach

        @if(($bar->show_tallies && perms(BarController::permsUser())) || perms(BarController::permsManage()))
            <a href="{{ route('bar.tally', ['barId' => $bar->human_id]) }}"
                    class="ui large basic button bottom attached">
                @lang('pages.bar.tallySummary')...
            </a>
        @endif
    </div>
@else
    @if(($bar->show_tallies && perms(BarController::permsUser())) || perms(BarController::permsManage()))
        <p>
            <a href="{{ route('bar.tally', ['barId' => $bar->human_id]) }}"
                    class="ui large basic button fluid">
                @lang('pages.bar.tallySummary')...
            </a>
        </p>
    @endif
@endif

@if(perms(BarController::permsManage()))
    <div class="ui two large basic buttons">
        <a href="{{ route('bar.history', ['barId' => $bar->human_id]) }}"
                class="ui button">
            @lang('pages.bar.allPurchases')
        </a>
        <a href="{{ route('bar.summary', ['barId' => $bar->human_id]) }}"
                class="ui button">
            @lang('misc.summary')
        </a>
    </div>
@endif
