@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.update.balance.subtitle'),
])

<br>

@foreach($data as $community)
@component('mail::block')
<b>{{ $community['name'] }}</b>  
@foreach($community['economies'] as $economy)
{{ $economy['name'] }}:
@foreach($economy['wallets'] as $wallet)
- <i>{{ $wallet['name'] }}:</i> {!! $wallet['balanceHtml'] !!}
@endforeach
@endforeach
@endcomponent
@endforeach

@component('mail::text')
@lang('mail.update.balance.pleaseTopUp')
@endcomponent

@endcomponent
