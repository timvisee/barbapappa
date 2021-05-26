@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.email.verify.subtitleRegistered'),
])

@component('mail::text')
@lang('mail.email.verify.registered')<br>

@lang('mail.email.verify.verifyBeforeUseAccount')<br>

@lang('mail.email.verify.soon', ['expire' => $expire])
@endcomponent

@component('mail::notice')
@lang('mail.email.verify.clickButtonToVerify')<br>

@component('mail::button', ['url' => route('email.verify', ['token' => $token])])
@lang('mail.email.verify.verifyButton')
@endcomponent
@endcomponent

@component('mail::text')
@lang('mail.email.verify.mayIgnore')<br>

@component('mail::htmlOnly')
@lang('mail.email.verify.manual')<br>

<a href="{{ route('email.verify', ['token' => $token]) }}">{{ route('email.verify', ['token' => $token]) }}</a><br>
@endcomponent
@endcomponent

@endcomponent
