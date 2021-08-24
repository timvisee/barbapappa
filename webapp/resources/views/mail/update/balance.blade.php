@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.update.balance.subtitle'),
])

<br>

@foreach($data as $community)
@foreach($community['economies'] as $economy)
@component('mail::block')
<b>{{ $community['name'] }}</b> ({{ $economy['name'] }}):

@foreach($economy['wallets'] as $wallet)
- [{{ $wallet['name'] }}]({{ $wallet['url'] }})  
  @lang('misc.balance'): {!! $wallet['balanceHtml'] !!}  
  @lang('misc.previously'): {!! $wallet['previousBalanceHtml'] !!} ({{ $wallet['previousPeriod'] }})  
  [@lang('misc.topUp')]({{ $wallet['topUpUrl'] }})

@endforeach
@endcomponent
@endforeach
@endforeach

@component('mail::text')
@lang('mail.update.balance.pleaseTopUp')<br>
<br>
@lang('mail.update.balance.noUpdateZeroBalance')
@endcomponent

@endcomponent
