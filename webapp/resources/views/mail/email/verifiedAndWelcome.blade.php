@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.email.verified.subtitle'),
])

@component('mail::text')
@lang('mail.email.verified.accountReady')<br>

@lang('mail.email.verified.startUsingSeeDashboard', ['app' => config('app.name')])
@endcomponent

@component('mail::button', ['url' => route('dashboard')])
@lang('pages.dashboard.yourPersonalDashboard')
@endcomponent

@component('mail::text')
@lang('mail.email.verified.configureEmailPreferences', ['app' => config('app.name')])
@endcomponent

@component('mail::button', ['url' => route('email.preferences')])
@lang('pages.emailPreferences')
@endcomponent

@endcomponent
