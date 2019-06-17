<div class="ui right sidebar messages inverted vertical menu">
    {{-- TODO: contents loaded through AJAX, show loader here? --}}

    {{-- TODO: should we leave this here? --}}
    @if(barauth()->isAuth())
        <a href="{{ route('dashboard') }}" class="item">
            <i class="glyphicons glyphicons-message-full"></i>
            {{-- TODO: translate button --}}
            Messages
        </a>
    @endif
</div>
