<div class="ui top vertical menu fluid">
    <h5 class="ui item header">
        {{ $header }}
    </h5>
    @forelse($currencies as $currency)
        <a class="item"
                href="{{ route('community.economy.currency.show', [
                    'communityId' => $community->id,
                    'economyId' => $economy->id,
                    'economyCurrencyId' => $currency->id
                ]) }}">
            {{ $currency->displayName}}
        </a>
    @endforeach

    <a href="{{ $button['link'] }}" class="ui bottom attached button">
        {{ $button['label'] }}
    </a>
</div>
