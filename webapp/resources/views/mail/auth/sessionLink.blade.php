@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.auth.sessionLink.subtitle'),
])

@component('mail::text')
@lang('mail.auth.sessionLink.soon', ['expire' => $expire])
@endcomponent

@component('mail::button', ['url' => route('auth.login', ['token' => $token])])
@lang('mail.auth.sessionLink.button')
@endcomponent

@component('mail::text')
@lang('mail.auth.sessionLink.mayIgnore')<br>

@component('mail::htmlOnly')
@lang('mail.auth.sessionLink.manual')<br>

<{{ route('auth.login', ['token' => $token]) }}><br>
@endcomponent
@endcomponent

@endcomponent
