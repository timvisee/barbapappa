@unless($breadcrumbs->isEmpty())
    <div class="ui breadcrumb">
        {{-- Show root item as home icon --}}
        <a class="section" href="{{ $breadcrumbs[0]->url }}" title="{{ $breadcrumbs[0]->title }}">
            <i class="halflings halflings-home"></i>
        </a>
        <i class="right angle icon divider"></i>

        @foreach($breadcrumbs->skip(1) as $breadcrumb)
            @if(!is_null($breadcrumb->url))
                <a class="{{ $loop->last ? 'active' : '' }} section"
                        href="{{ $breadcrumb->url }}">
                    {{ $breadcrumb->title }}
                </a>
            @else
                <div class="{{ $loop->last ? 'active' : '' }} section">
                    {{ $breadcrumb->title }}
                </div>
            @endif

            @unless($loop->last)
                <div class="divider"> / </div>
            @endunless
        @endforeach
    </div>
@endunless
