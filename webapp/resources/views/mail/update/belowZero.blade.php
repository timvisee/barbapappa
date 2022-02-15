@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.update.belowZero.subtitle'),
])

<br>

@php
    $details = [];
    $details[] = [
        'key' => __('misc.wallet'),
        'valueHtml' => '<a href="' . route('community.wallet.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'walletId' => $wallet->id,
            ]) . '">' . $wallet->name . '</a>',
    ];
    $details[] = [
        'key' => __('misc.balance'),
        'valueHtml' => $wallet->formatBalance(BALANCE_FORMAT_COLOR),
    ];
    if($community != null)
        $details[] = [
            'key' => __('misc.community'),
            'valueHtml' => '<a href="' . route('community.show', [
                    'communityId' => $community->human_id
                ]) . '">'
                . $community->name . '</a>',
        ];
@endphp
@component('mail::block')
@component('mail::details', ['table' => $details])
@endcomponent
<br>

@component('mail::mini_button', ['url' => $topUpUrl, 'color' => 'red'])
@lang('misc.topUp')
@endcomponent
@component('mail::mini_button', ['url' => $walletUrl, 'color' => 'grey', 'link' => true])
@lang('misc.view')
@endcomponent
@component('mail::mini_button', ['url' => $transactionsUrl, 'color' => 'grey', 'link' => true])
@lang('misc.transactions')
@endcomponent
<br>
@endcomponent
<br>

@component('mail::text')
@lang('mail.update.belowZero.pleaseTopUp')
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
