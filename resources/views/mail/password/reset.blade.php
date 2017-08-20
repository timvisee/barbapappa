@component('mail::message', [
    'recipient' => $recipient,
    'subject' => $subject,
    'subtitle' => 'Your password has been changed.',
])

@component('mail::text')
We're just notifying you for security reasons.

From now on, use your new password to login to your account.
@endcomponent

@component('mail::button', ['url' => route('dashboard')])
Your personal dashboard
@endcomponent

<br>

@component('mail::notice')
@if(isset($token))
If you didn't change your password yourself, please change it as soon as possible using the following link and token.

**Link:** [{{ route('password.reset') }}]({{ route('password.reset', ['token' => $token]) }})<br>
**Token:** _{{ $token }}_

Or [contact]({{ route('contact') }}) the {{ config('app.name') }} team as soon as possible about this security issue.
@else
If you received this message but haven't changed your password, please [contact]({{ route('contact') }}) the {{ config('app.name') }} team as soon as possible.
@endif
@endcomponent

@endcomponent