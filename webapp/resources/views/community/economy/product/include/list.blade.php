{{-- TODO: implement pagination --}}

<div class="ui top vertical menu fluid{{ !empty($class) ? ' ' . implode(' ', $class) : '' }}">
    @foreach($groups as $group)
        {{-- Header --}}
        @if(isset($group['header']))
            <h5 class="ui item header">
                {{ $group['header'] }}
            </h5>
        @endif

        {{-- Products --}}
        @forelse($group['products'] as $product)
            <a class="item"
                    href="{{ route('community.economy.product.show', [
                        // TODO: this is not efficient
                        'communityId' => $product->economy->community->human_id,
                        'economyId' => $product->economy_id,
                        'productId' => $product->id,
                    ]) }}">
                {{ $product->displayName() }}
                {{-- {!! $product->formatCost(BALANCE_FORMAT_LABEL); !!} --}}

                {{-- TODO: show some other stat here --}}
                <span class="sub-label">
                    @include('includes.humanTimeDiff', ['time' => $product->updated_at ?? $product->created_at])
                </span>
            </a>
        @empty
            <i class="item">@lang('pages.products.noProducts')</i>
        @endforelse
    @endforeach

    {{-- Bottom button --}}
    @if(isset($button))
        <a href="{{ $button['link'] }}" class="ui bottom attached button">
            {{ $button['label'] }}
        </a>
    @endif
</div>
