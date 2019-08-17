@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.update.balance.subtitle'),
])

{{-- @component('mail::text') --}}
{{-- @lang('mail.auth.sessionLink.soon', ['expire' => 'null']) --}}
{{-- @endcomponent --}}

{{-- @component('mail::button', ['url' => route('auth.login', ['token' => 'null'])]) --}}
{{-- @lang('mail.auth.sessionLink.button') --}}
{{-- @endcomponent --}}

{{-- @component('mail::text') --}}
{{-- @lang('mail.auth.sessionLink.manual')<br> --}}

{{-- {{ route('auth.login', ['token' => 'null']) }}<br> --}}
{{-- @endcomponent --}}

@endcomponent
