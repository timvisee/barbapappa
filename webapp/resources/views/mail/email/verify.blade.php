@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.email.verify.subtitle'),
])

@component('mail::text')
@lang('mail.email.verify.addNewEmail')<br>

@lang('mail.email.verify.verifyBeforeUseEmail')<br>

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

@lang('mail.email.verify.manual')<br>

{{ route('email.verify', ['token' => $token]) }}<br>
@endcomponent

@endcomponent
