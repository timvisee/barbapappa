@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.password.request.subtitle'),
])

@component('mail::text')
@lang('mail.password.request.requestedReset')<br>

@lang('mail.password.request.visitResetPage')<br>

@lang('mail.password.request.soon', ['expire' => $expire])
@endcomponent

@component('mail::notice')
@lang('mail.password.request.clickButtonToReset')<br>

@component('mail::button', ['url' => route('password.reset', ['token' => $token])])
@lang('mail.password.request.resetButton')
@endcomponent
@endcomponent

@component('mail::text')
@lang('mail.password.request.mayIgnore')<br>

@component('mail::htmlOnly')
@lang('mail.password.request.manual')<br>

<a href="{{ route('password.reset', ['token' => $token]) }}">{{ route('password.reset', ['token' => $token]) }}</a><br>
@endcomponent
@endcomponent

@endcomponent
