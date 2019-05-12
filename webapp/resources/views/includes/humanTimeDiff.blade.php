{{-- Render the difference between the given time and now in a humanly readable --}}
{{-- format, use: `@include('includes.humanTimeDiff', ['time' => $time])` --}}

<span title="{{ $time }}"> {{ $time->diffForHumans(null, null, $short ?? false) }}</span>
