@unless($breadcrumbs->isEmpty())
    <div class="ui breadcrumb">
        @foreach($breadcrumbs as $breadcrumb)

            @if(!is_null($breadcrumb->url) && !$loop->last)
                <a class="section" href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
                <div class="divider"> / </div>
            @else
                <div class="active section">{{ $breadcrumb->title }}</div>
            @endif

        @endforeach
    </div>
@endunless
