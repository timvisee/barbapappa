@component('mail::message', [
    'recipient' => $recipient,
    'subject' => $subject,
    'subtitle' => __('mail.password.request.subtitle'),
])

@component('mail::text')
@lang('mail.password.request.requestedReset')<br>

@lang('mail.password.request.visitResetPage')<br>

@lang('mail.password.request.soon', ['hours' => 24])
@endcomponent

@component('mail::notice')
@lang('mail.password.request.clickButtonToReset')<br>

@component('mail::button', ['url' => route('password.reset', ['token' => $token])])
@lang('mail.password.request.resetButton')
@endcomponent
@endcomponent

@component('mail::text')
@lang('mail.password.request.manual')<br>

**@lang('general.link'):** [{{ route('password.reset') }}]({{ route('password.reset', ['token' => $token]) }})<br>
**@lang('general.token'):** _{{ $token }}_<br>

@lang('mail.password.request.notRequested')
@endcomponent

@endcomponent