@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('auth.passwordChanged'),
])

@component('mail::text')
@lang('mail.password.reset.forSecurity')<br>

@lang('mail.password.reset.useNewPassword')
@endcomponent

@component('mail::button', ['url' => route('dashboard')])
@lang('pages.dashboard.yourPersonalDashboard')
@endcomponent

<br>

@component('mail::notice')
@if(isset($token))
@lang('mail.password.reset.noChangeThenReset')<br>

{{ route('password.reset', ['token' => $token, 'compromised' => true]) }}<br>

@lang('mail.password.reset.orContact', ['contact' => route('contact'), 'app' => config('app.name')])
@else
@lang('mail.password.reset.noChangeThenContact', ['contact' => route('contact'), 'app' => config('app.name')])
@endif
@endcomponent

@endcomponent
