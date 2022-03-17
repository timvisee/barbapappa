@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.email.remove.subtitle'),
])

@component('mail::text')
@lang('mail.email.remove.forSecurity')<br>
@endcomponent

@component('mail::notice')
@lang('mail.email.remove.noRemovedThenContact', ['contact' => route('contact'), 'app' => config('app.name')])
@endcomponent

@endcomponent
