@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.update.belowZero.subtitle'),
])

<br>

@component('mail::block')
<b>{{ $community->name }}</b> ({{ $economy->name }}):

- [{{ $wallet->name }}]({{ $walletUrl }})  
@lang('misc.balance'): {!! $wallet->formatBalance(BALANCE_FORMAT_COLOR) !!}  

@component('mail::mini_button', ['url' => $topUpUrl, 'color' => $wallet['isNegative'] ? 'blue' : 'grey'])
@lang('misc.topUp')
@endcomponent
@component('mail::mini_button', ['url' => $walletUrl, 'color' => 'grey'])
@lang('misc.view')
@endcomponent
@component('mail::mini_button', ['url' => $transactionsUrl, 'color' => 'grey'])
@lang('misc.transactions')
@endcomponent
@endcomponent

@component('mail::text')
@lang('mail.update.balance.pleaseTopUp')
@endcomponent

@endcomponent
