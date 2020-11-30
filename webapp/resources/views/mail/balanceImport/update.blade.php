@component('mail::message', [
    'user' => $user,
    'name' => $user_name,
    'subject' => $subject,
    'subtitle' => $subtitle,
])

<br>

@php
    $details = [];
    if($wallet != null) {
        $details[] = [
            'key' => __('misc.balance'),
            'valueHtml' => $wallet->formatBalance(BALANCE_FORMAT_COLOR),
        ];
        $details[] = [
            'key' => __('misc.wallet'),
            'valueHtml' => '<a href="' . route('community.wallet.show', [
                    'communityId' => $wallet->economyMember->economy->community->human_id,
                    'economyId' => $wallet->economyMember->economy_id,
                    'walletId' => $wallet->id,
                ]) . '">' . $wallet->name . '</a>',
        ];
    } else if($change != null) {
        $details[] = [
            'key' => __('misc.balance'),
            'valueHtml' => $change->formatAmount(BALANCE_FORMAT_COLOR),
        ];
    }
    if($change != null)
        $details[] = [
            'key' => __('misc.imported'),
            'value' => $change->created_at->toFormattedDateString()
                . ' (' . $change->created_at->diffForHumans() . ') '
                . ' ' . __('misc.by') . ' '
                . $change->submitter->name,
        ];
    if($event != null)
        $details[] = [
            'key' => __('misc.event'),
            'value' => $event->name,
        ];
    if($system != null)
        $details[] = [
            'key' => __('misc.source'),
            'value' => $system->name,
        ];
@endphp
@if(!empty($details))
@component('mail::details', ['table' => $details])
@endcomponent
@endif

<br>

@if(isset($message))
@component('mail::block')
{!! $message !!}
@endcomponent
<br>
@endif

@if(!empty($invite_to_bar))
@component('mail::notice')
@lang('mail.balanceImport.update.joinBarDescription', ['name' => $invite_to_bar->name])<br>

{{-- TODO: this should lead to payment page --}}
@component('mail::button', ['url' => route('bar.join', ['barId' => $invite_to_bar->human_id, 'code' => $invite_to_bar->password])])
@lang('mail.balanceImport.update.joinBarButton', ['name' => $invite_to_bar->name])
@endcomponent
@endcomponent
@endif

@component('mail::text')
@lang('mail.balanceImport.update.pleaseTopUp')<br>
<br>
@lang('mail.balanceImport.update.noUpdateZeroBalance')
@endcomponent

@endcomponent
