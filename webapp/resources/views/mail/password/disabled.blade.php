@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('auth.passwordDisabled'),
])

@component('mail::text')
@lang('mail.password.disabled.forSecurity')<br>
@endcomponent

@component('mail::notice')
@if(isset($token))
@lang('mail.password.disabled.noDisabledThenReset')<br>

{{ route('password.reset', ['token' => $token, 'compromised' => true]) }}<br>

@lang('mail.password.disabled.orContact', ['contact' => route('contact'), 'app' => config('app.name')])
@else
@lang('mail.password.disabled.noDisabledThenContact', ['contact' => route('contact'), 'app' => config('app.name')])
@endif
@endcomponent

@endcomponent
