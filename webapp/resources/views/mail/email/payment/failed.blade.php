{{-- Generic email for various payment failure states --}}

@component('mail::message', [
    'user' => $user,
    'subject' => $subject,
    'subtitle' => __('mail.payment.failed.subtitle'),
])

@if($payment != null)
@component('mail::text')
@lang('mail.payment.failed.state' . ucfirst($payment->stateIdentifier()))
@endcomponent
@endif

@php
    $details = [];
    if($payment != null)
        $details[] = [
            'key' => __('misc.amount'),
            'valueHtml' => $payment->formatCost(BALANCE_FORMAT_COLOR),
        ];
    if($wallet != null) {
        $details[] = [
            'key' => __('misc.wallet'),
            'valueHtml' => '<a href="' . route('community.wallet.show', [
                    'communityId' => $wallet->economy->community->human_id,
                    'economyId' => $wallet->economy_id,
                    'walletId' => $wallet->id,
                ]) . '">' . $wallet->name . '</a>',
        ];
        $details[] = [
            'key' => __('misc.balance'),
            'valueHtml' => $wallet->formatBalance(BALANCE_FORMAT_COLOR),
        ];
    }
    if($payment != null) {
        $details[] = [
            'key' => __('misc.payment'),
            'valueHtml' => '<a href="' . route('payment.show', ['paymentId' => $payment->id]) . '">'
                . $payment->service->displayName() . '</a>',
        ];
        $details[] = [
            'key' => __('misc.state'),
            'value' => $payment->stateName(),
        ];
    }
    if($community != null)
        $details[] = [
            'key' => __('misc.community'),
            'valueHtml' => '<a href="' . route('community.show', [
                    'communityId' => $community->human_id
                ]) . '">'
                . $community->name . '</a>',
        ];
@endphp
@if(!empty($details))
@component('mail::details', ['table' => $details])
@endcomponent
@endif
<br>

@if($community != null)
@component('mail::button', ['url' => route('payment.show', [
    'paymentId' => $payment->id
])])
@lang('pages.payments.viewPayment')
@endcomponent
@endif
<br>

@endcomponent
