@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.update.receipt.subtitle'),
])

<br>

@component("mail::markdownOnly")---@endcomponent

@foreach($data as $community)
@foreach($community['economies'] as $economy)
@foreach($economy['wallets'] as $wallet)
@component('mail::block')
<strong>@component('mail::link', ['url' => $wallet['url']]){{ $wallet['name'] }}@endcomponent</strong><br><em>@lang('misc.in') {{ $community['name'] }} ({{ $economy['name'] }})</em>

@component('mail::receipt', ['receipt' => $wallet['receipt']])@endcomponent
<br>

@lang('misc.balance'): {!! $wallet['balanceHtml'] !!}

@component('mail::mini_button', ['url' => $wallet['topUpUrl'], 'color' => $wallet['isNegative'] ? 'blue' : 'grey'])
@lang('misc.topUp')
@endcomponent
@component('mail::mini_button', ['url' => $wallet['url'], 'color' => 'grey'])
@lang('misc.view')
@endcomponent
@component('mail::mini_button', ['url' => $wallet['statsUrl'], 'color' => 'grey'])
@lang('misc.stats')
@endcomponent

@component("mail::markdownOnly")---@endcomponent

@endcomponent
@endforeach
@endforeach
@endforeach

@component('mail::text')
@lang('mail.update.receipt.pleaseTopUp')<br>
@endcomponent

@endcomponent
