@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.auth.sessionLink.subtitle'),
])

@component('mail::text')
{{-- TODO: show dynamic number of minutes here --}}
@lang('mail.auth.sessionLink.soon', ['minutes' => 30])
@endcomponent

@component('mail::button', ['url' => route('auth.login', ['token' => $token])])
@lang('mail.auth.sessionLink.button')
@endcomponent

@component('mail::text')
@lang('mail.auth.sessionLink.manual')<br>

[{{ route('auth.login', ['token' => $token]) }}]({{ route('auth.login', ['token' => $token]) }})<br>
@endcomponent

@endcomponent
