<div class="ui top vertical menu fluid{{ !empty($class) ? ' ' . implode(' ', $class) : '' }}">
    @foreach($groups as $group)
        {{-- Header --}}
        @if(isset($group['header']))
            <h5 class="ui item header">
                {{ $group['header'] }}
            </h5>
        @endif

        {{-- Payment services --}}
        @forelse($group['services'] as $service)
            <a class="item"
                    href="{{ route('community.economy.payservice.show', [
                        // TODO: this is not efficient
                        'communityId' => $service->economy->community->human_id,
                        'economyId' => $service->economy_id,
                        'serviceId' => $service->id,
                    ]) }}">
                {{ $service->displayName() }}

                {{-- TODO: show some other stat here --}}
                <span class="sub-label">
                    @include('includes.humanTimeDiff', ['time' => $service->updated_at ?? $service->created_at])
                </span>
            </a>
        @empty
            <i class="item">@lang('pages.paymentService.noServices')</i>
        @endforelse
    @endforeach

    {{-- Bottom button --}}
    @if(isset($button))
        <a href="{{ $button['link'] }}" class="ui bottom attached button">
            {{ $button['label'] }}
        </a>
    @endif
</div>
