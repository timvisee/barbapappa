@component('mail::layout', ['subject' => $subject])

    {{-- Title --}}
    @component('mail::title')
        {{ trans_random('general.hellos') }} {{ $user->first_name }}

        @if(isset($subtitle))
            @slot('lead')
                {!! $subtitle !!}
            @endslot
        @endif
    @endcomponent

    {{-- Body --}}
    {!! $slot !!}

    @component('mail::text')
        @lang('mail.signature.signoff', ['app' => config('app.name')])
    @endcomponent

@endcomponent
