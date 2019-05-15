{{-- Render the difference between the given time and now in a humanly readable --}}
{{-- format, use: `@include('includes.humanTimeDiff', ['time' => $time])` --}}

@unless($absolute ?? false)
    <span title="{{ $time }}"> {{ $time->diffForHumans(null, null, $short ?? false) }}</span>
@else
    <span title="{{ $time }}"> {{ $time->shortAbsoluteDiffForHumans(null, null, $short ?? false) }}</span>
@endif

