@include('mail.inc.fixLocale')

@component('mail::message', [
    'recipient' => $recipient,
    'subject' => $subject,
    'subtitle' => __('mail.payment.completed.subtitle'),
])

@component('mail::text')
@lang('mail.payment.completed.paymentReceived')<br>

@lang('mail.payment.completed.amountReadyToUse')
@endcomponent


@component('mail::details', ['table' => [
    [
        'key' => 'Deposited',
        'valueHtml' => $payment->formatCost(BALANCE_FORMAT_COLOR),
    ],
    [
        'key' => 'Wallet',
        'value' => $wallet->name,
    ],
    [
        'key' => 'Balance',
        'valueHtml' => $wallet->formatBalance(BALANCE_FORMAT_COLOR),
    ],
    [
        'key' => 'Payment method',
        'value' => $payment->service->displayName(),
    ],
    [
        'key' => 'Community',
        'value' => $community->name,
    ],
]])
@endcomponent


{{-- TODO: show payment information --}}

{{-- TODO: link to the bar, transaction and wallet --}}

@if($community != null)
@component('mail::button', ['url' => route('community.show', ['communityId' => $community->id])])
@lang('pages.community.visitCommunity')<br>
@endcomponent
@endif

@endcomponent
