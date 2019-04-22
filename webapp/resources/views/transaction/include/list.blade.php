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
                {!! $transaction->formatCost(BALANCE_FORMAT_LABEL); !!}

                <span class="sub-label">
                    @include('includes.humanTimeDiff', ['time' => $transaction->updated_at ?? $transaction->created_at])
                </span>
            </a>
        @endforeach
    @endforeach

    {{-- Bottom button --}}
    @if(isset($button))
        <a href="{{ $button['link'] }}" class="ui bottom attached button">
            {{ $button['label'] }}
        </a>
    @endif
</div>
