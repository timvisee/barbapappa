@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.update.balance.subtitle'),
])

<br>

@component("mail::markdownOnly")---@endcomponent

@foreach($data as $community)
@foreach($community['economies'] as $economy)
@foreach($economy['wallets'] as $wallet)
@component('mail::block')
<strong>@component('mail::link', ['url' => $wallet['url']]){{ $wallet['name'] }}@endcomponent</strong><br><em>@lang('misc.in') {{ $community['name'] }} ({{ $economy['name'] }})</em>

@unless($wallet['balanceSame'])
@lang('misc.balance'): {!! $wallet['balanceHtml'] !!}  
@lang('misc.previously'): {!! $wallet['previousBalanceHtml'] !!} ({{ $wallet['previousPeriod'] }})  
@else
@lang('misc.balance'): {!! $wallet['balanceHtml'] !!} ({{ strtolower(__('misc.sameAs')) }} {{ $wallet['previousPeriod'] }})  
@endif

@component('mail::mini_button', ['url' => $wallet['topUpUrl'], 'color' => $wallet['isNegative'] ? 'blue' : 'grey'])
@lang('misc.topUp')
@endcomponent
@component('mail::mini_button', ['url' => $wallet['url'], 'color' => 'grey'])
@lang('misc.view')
@endcomponent
@component('mail::mini_button', ['url' => $wallet['statsUrl'], 'color' => 'grey'])
@lang('misc.stats')
@endcomponent

@if($wallet['receipt'])
<br>
@component('mail::receipt', ['receipt' => $wallet['receipt']])@endcomponent
@endif

@component("mail::markdownOnly")---@endcomponent

@endcomponent
@endforeach
@endforeach
@endforeach

@component('mail::text')
@lang('mail.update.balance.pleaseTopUp')<br>
<br>
@lang('mail.update.balance.noUpdateZeroBalance')
@endcomponent

@unless($user->mail_receipt)
@component('mail::notice')
@lang('mail.receipts.receiveReceiptAfterEachVisit')

@component('mail::link', ['url' => route('email.preferences')]) 
{{ strtolower(__('pages.emailPreferences')) }}
@endcomponent
@endcomponent
@endunless

@endcomponent
