{{-- TODO: implement pagination --}}

<div class="ui top vertical menu fluid">
    @foreach($groups as $group)
        {{-- Header --}}
        @if(isset($group['header']))
            <h5 class="ui item header">
                {{ $group['header'] }}
            </h5>
        @endif

        {{-- Mutations --}}
        @forelse($group['mutations'] as $mutation)
            <a class="item"
                    href="{{ route('transaction.mutation.show', [
                        'transactionId' => $mutation->transaction_id,
                        'mutationId' => $mutation->id,
                    ]) }}">
                {{ $mutation->describe() }}
                {!! $mutation->formatAmount(BALANCE_FORMAT_LABEL); !!}

                <span style="color: gray; float: right;">
                    {{ $mutation->created_at->diffForHumans() }}
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
