<div class="ui vertical menu fluid">
    {{--
        <div class="item">
            <div class="ui transparent icon input">
                <input type="text" placeholder="Search bars...">
                <i class="icon glyphicons glyphicons-search link"></i>
            </div>
        </div>
    --}}

    @forelse($bars as $bar)
        <a href="{{ route('bar.show', ['barId' => $bar->id ]) }}" class="item">
            {{ $bar->name }}
        </a>
    @empty
        <div class="item">
            <i>@lang('pages.bar.noBars')</i>
        </div>
    @endforelse
</div>
