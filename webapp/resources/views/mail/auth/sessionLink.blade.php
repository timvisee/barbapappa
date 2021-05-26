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

@lang('mail.auth.sessionLink.manual')<br>

<a href="{{ route('auth.login', ['token' => $token]) }}">{{ route('auth.login', ['token' => $token]) }}</a><br>
@endcomponent

@endcomponent
