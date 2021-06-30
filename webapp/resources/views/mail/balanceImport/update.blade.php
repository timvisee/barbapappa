@component('mail::message', [
    'user' => $user,
    'name' => $user_name,
    'subject' => $subject,
    'subtitle' => $subtitle,
])

<br>

@php
    $details = [];
    $details[] = [
        'key' => __('misc.balance'),
        'valueHtml' => $balance->formatAmount(BALANCE_FORMAT_COLOR),
    ];
    if($balance_change != null)
        $details[] = [
            'key' => __('misc.difference'),
            'valueHtml' => $balance_change->formatAmount(BALANCE_FORMAT_COLOR),
        ];
    // TODO: remove this?
    if(isset($wallet) && $wallet != null)
        $details[] = [
            'key' => __('misc.wallet'),
            'valueHtml' => '<a href="' . route('community.wallet.show', [
                    'communityId' => $wallet->economyMember->economy->community->human_id,
                    'economyId' => $wallet->economyMember->economy_id,
                    'walletId' => $wallet->id,
                ]) . '">' . $wallet->name . '</a>',
        ];
    if($last_change != null)
        $details[] = [
            'key' => __('misc.lastImport'),
            'value' => $last_change->created_at->toFormattedDateString()
                . ' (' . $last_change->created_at->diffForHumans() . ') '
                . ' ' . __('misc.by') . ' '
                . $last_change->submitter->name,
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

    if(isset($wallet) && $wallet != null)
        $topUpUrl = route('community.wallet.topUp', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'walletId' => $wallet->id]);
    elseif(isset($economy) && $economy != null)
        $topUpUrl = route('community.wallet.quickTopUp', ['communityId' => $community->human_id, 'economyId' => $economy->id]);
    else
        $topUpUrl = null;
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

{{-- Bar sign up button for new users --}}
{{-- TODO: this should lead to payment page --}}
@if($invite_to_bar)
@component('mail::notice')
@lang('mail.balanceImport.update.joinBarDescription', ['name' => $bar->name])<br>

@component('mail::button', ['url' => route('bar.join', ['barId' => $bar->human_id, 'code' => $bar->password])])
@lang('mail.balanceImport.update.joinBarButton', ['name' => $bar->name])
@endcomponent  
@if($topUpUrl != null)


@lang('mail.balanceImport.update.afterJoinTopUp', [
    'here' => '<a href="' . $topUpUrl . '">' . lcfirst(__('misc.here')) . '</a>',
])
@endif
@endcomponent
@endif

{{-- Email verification button --}}
@if($request_to_verify)
@component('mail::notice')
@lang('mail.balanceImport.update.verifyMailDescription')<br>

@component('mail::button', ['url' => route('account.emails.unverified')])
@lang('mail.balanceImport.update.verifyMailButton')
@endcomponent
@endcomponent
@endif

{{-- Top-up button, if user has joined --}}
@if($joined && $topUpUrl != null)
@component('mail::notice')
@lang('mail.balanceImport.update.payInAppDescription')<br>

@component('mail::button', ['url' => $topUpUrl])
@lang('mail.balanceImport.update.payInAppButton')
@endcomponent
@endcomponent
@endif

@component('mail::text')
@lang('mail.balanceImport.update.pleaseTopUp')<br>
<br>
@lang('mail.balanceImport.update.noUpdateZeroBalance')
@endcomponent

@endcomponent
