@component('mail::message', [
    'recipient' => $recipient,
    'subject' => $subject,
    'subtitle' => 'First of all, welcome to the club!',
])

@component('mail::text')
Your email address has just been verified and your account is now ready.

To start using {{ config('app.name') }}, take a look at your personalized dashboard.
@endcomponent

@component('mail::button', ['url' => route('dashboard')])
Your personal dashboard
@endcomponent

@component('mail::text')
To configure how often you receive email updates from {{ config('app.name') }}, check out your email preferences panel.
@endcomponent

@component('mail::button', ['url' => route('email.preferences')])
Email preferences
@endcomponent

@endcomponent