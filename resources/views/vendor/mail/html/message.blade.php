@component('mail::layout', ['subject' => $subject])

    {{-- Title --}}
    @component('mail::title')
        Hello Tim

        @if(isset($subtitle))
            @slot('lead')
                {{ $subtitle }}
            @endslot
        @endif
    @endcomponent

    {{-- Body --}}
    {!! $slot !!}

    @component('mail::text')
        Thanks,<br>
        The {{ config('app.name') }} team
    @endcomponent

@endcomponent
