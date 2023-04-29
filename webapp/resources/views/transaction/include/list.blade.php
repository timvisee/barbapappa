{{-- TODO: implement pagination --}}

<div class="ui top vertical menu fluid">
    @foreach($groups as $group)
        {{-- Header --}}
        @if(isset($group['header']))
            <h5 class="ui item header">
                {{ $group['header'] }}
            </h5>
        @endif

        {{-- Transactions --}}
        @forelse($group['transactions'] as $transaction)
            <a class="item"
                    href="{{ route('transaction.show', [
                        'transactionId' => $transaction->id,
                    ]) }}">
                {{ $transaction->describe() }}
                {!! $transaction->formatCost(BALANCE_FORMAT_LABEL, false, $wallet ?? null); !!}

                <span class="sub-label">
                    {{-- Icon for delayed purchases --}}
                    @if($transaction->isDelayed())
                        <span class="halflings halflings-hourglass"></span>
                    @endif

                    {{-- Icon for kiosk purchases --}}
                    @if($transaction->initiated_by_kiosk)
                        <span class="halflings halflings-shopping-cart"></span>
                    @endif

                    @include('includes.humanTimeDiff', [
                        'time' => $transaction->updated_at ?? $transaction->created_at,
                        'absolute' => true,
                        'short' => true,
                    ])
                </span>
            </a>
        @empty
            <i class="item">@lang('pages.transactions.noTransactions')...</i>
        @endforelse
    @endforeach

    {{-- Bottom button --}}
    @if(isset($button))
        <a href="{{ $button['link'] }}" class="ui bottom attached button">
            {{ $button['label'] }}
        </a>
    @endif
</div>
