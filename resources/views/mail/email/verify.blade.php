@component('mail::message', [
    'recipient' => $recipient,
    'subject' => $subject,
    'subtitle' => __('mail.email.verify.subtitle'),
])

@component('mail::text')
@lang('mail.email.verify.addNewEmail')<br>

@lang('mail.email.verify.verifyBeforeUseEmail')<br>

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

**@lang('general.link'):** [{{ route('email.verify') }}]({{ route('email.verify', ['token' => $token]) }})<br>
**@lang('general.token'):** _{{ $token }}_
@endcomponent

@endcomponent