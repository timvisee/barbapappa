@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.email.verify.subtitleRegistered'),
])

@component('mail::text')
@lang('mail.email.verify.registered')<br>

@lang('mail.email.verify.verifyBeforeUseAccount')<br>

@lang('mail.email.verify.soon', ['hours' => 48])
@endcomponent

@component('mail::notice')
@lang('mail.email.verify.clickButtonToVerify')<br>

@component('mail::button', ['url' => route('email.verify', ['token' => $token])])
@lang('mail.email.verify.verifyButton')
@endcomponent
@endcomponent

@component('mail::text')
@lang('mail.email.verify.manual')<br>

**@lang('misc.link'):** [{{ route('email.verify') }}]({{ route('email.verify', ['token' => $token]) }})<br>
**@lang('misc.token'):** _{{ $token }}_
@endcomponent

@endcomponent
