<div class="ui vertical menu fluid">
    @if(isset($header))
        <h5 class="ui item header">{{ $header }}</h5>
    @endif

    {{--
        <div class="item">
            <div class="ui transparent icon input">
                {{ Form::text('search', '', ['placeholder' => 'Search bars...']) }}
                <i class="icon link">
                    <span class="glyphicons glyphicons-search"></span>
                </i>
            </div>
        </div>
    --}}

    @forelse($bars as $bar)
        <a href="{{ route('bar.show', ['barId' => $bar->human_id ]) }}" class="item">
            {{ $bar->name }}
        </a>
    @empty
        <div class="item">
            <i>@lang('pages.bar.noBars')</i>
        </div>
    @endforelse
</div>
