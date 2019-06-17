<div class="item">
    <p>
        {{ $message }}
    </p>

    {{-- <a href="#" class="ui compact inverted tiny icon button green basic"> --}}
    {{--     <i class="glyphicons glyphicons-tick"></i> --}}
    {{-- </a> --}}

    @if(isset($actions))
        @foreach($actions as $action)
            <a href="{!! $action['url'] !!}"
                    class="ui compact inverted tiny button primary basic">
                {{ $action['name'] }}
            </a>
        @endforeach
    @endif

    {{-- @include('includes.humanTimeDiff', ['time' => $updated_at, 'short' => true]) --}}
</div>
