@component('mail::message', [
    'recipient' => $recipient,
    'subject' => $subject,
    'subtitle' => 'You\'re about to verify your email address.',
])

@component('mail::text')
You've just added a new email address to your account.

Before you can use it on our service, you need to verify it.

Please do this as soon as possible as the verification link expires **within 48 hours**.
@endcomponent

@component('mail::notice')
Please click the following button to verify your email address.

@component('mail::button', ['url' => route('email.verify', ['token' => $token])])
Verify your email address
@endcomponent
@endcomponent

@component('mail::text')
If the above button doesn't work, you may use the following link and token to verify your email address manually.

**Link:** [{{ route('email.verify') }}]({{ route('email.verify', ['token' => $token]) }})<br>
**Token:** _{{ $token }}_
@endcomponent

@endcomponent