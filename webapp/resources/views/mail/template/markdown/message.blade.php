@component('mail::layout', ['subject' => $subject])

{{-- Title --}}
@component('mail::title')
{{ trans_random('general.hellos') }} {{ $recipient->getFirstName() }}

@if(isset($subtitle))
@slot('lead')
{{ $subtitle }}
@endslot
@endif
@endcomponent

{{-- Body --}}
{{ $slot }}

@component('mail::text')
{{ trans_random('mail.signature.caption') }}<br>
@lang('mail.signature.name', ['app' => config('app.name')])
@endcomponent

@endcomponent
