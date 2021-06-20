@unless($breadcrumbs->isEmpty())
    <div class="ui breadcrumb">
        @foreach($breadcrumbs as $breadcrumb)
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
