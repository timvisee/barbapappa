@component('mail::message', [
    'recipient' => $recipient,
    'subject' => $subject,
    'subtitle' => 'We\'ll help you configuring a new password.',
])

@component('mail::text')
You've just requested to reset your password.

Simply visit the password reset page and enter your preferred password.

Please do this as soon as possible as the reset link expires **within 24 hours**.
@endcomponent

@component('mail::notice')
Please click the following button to reset your password.

@component('mail::button', ['url' => route('password.reset', ['token' => $token])])
Reset your password
@endcomponent
@endcomponent

@component('mail::text')
If the above button doesn't work, you may use the following link and token to reset your password manually.<br>

**Link:** [{{ route('password.reset') }}]({{ route('password.reset', ['token' => $token]) }})<br>
**Token:** _{{ $token }}_

If you haven't requested a password reset, you may ignore this email message.
@endcomponent

@endcomponent