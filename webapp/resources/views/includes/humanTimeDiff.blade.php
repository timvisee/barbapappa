{{-- Render the difference between the given time and now in a humanly readable --}}
{{-- format, use: `@include('includes.humanTimeDiff', ['time' => $time])` --}}

{{-- TODO: use correct current timezone --}}

@unless($absolute ?? false)
    <span title="{{ $time }} (UTC)"> {{ $time->diffForHumans(null, null, $short ?? false) }}</span>
@else
    <span title="{{ $time }} (UTC)"> {{ $time->shortAbsoluteDiffForHumans(null, null, $short ?? false) }}</span>
@endif

