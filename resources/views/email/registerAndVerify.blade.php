@component('mail::message', ['subject' => $subject, 'subtitle' => 'Your account is almost ready.'])

@component('mail::text')
Thank you for registering an account.

Before you can use our service, you need to verify your email address.

Please do this as soon as possible as the verification link expires within 48 hours.
@endcomponent

@component('mail::notice')
Please click the following button to verify your email address.

@component('mail::button', ['url' => route('email.verify', ['token' => $token])])
Verify my email address
@endcomponent
@endcomponent

@component('mail::text')
If the above button doesn't work, you may use the following link and token to verify your email address manually.<br>

**Link:** [{{ route('email.verify') }}]({{ route('email.verify', ['token' => $token]) }})<br>
**Token:** _{{ $token }}_
@endcomponent

@endcomponent