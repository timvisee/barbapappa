<div class="ui right sidebar messages inverted vertical menu">
    <div class="item header">TODO</div>

    @if(barauth()->isAuth())
        <a href="{{ route('dashboard') }}" class="item">
            <i class="glyphicons glyphicons-message-full"></i>
            {{-- TODO: translate button --}}
            Messages
        </a>
    @endif
</div>
