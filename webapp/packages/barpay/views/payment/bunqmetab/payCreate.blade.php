<div class="ui divider hidden"></div>

<a href="{{ \Request::getRequestUri() }}" class="ui loading button primary big">...</a>

<div class="ui left pointing label">
    @if(($secondsPassed = $payment->created_at?->diffInSeconds()) < 6)
        @lang('barpay::misc.justASecond')
    @elseif($secondsPassed < 60)
        @lang('barpay::misc.takingSomeTime')
    @else
        @lang('barpay::misc.tryAgainLater')
    @endif
</div>

<div class="ui divider hidden"></div>

<div class="ui info message">
    @lang('barpay::payment.bunqmetab.waitOnCreate')<br>
    <br>
    @lang('barpay::payment.bunqmetab.handledByBunq')
</div>

<div class="ui divider hidden"></div>

{{-- TODO: find a better refreshing method! --}}
<meta http-equiv="refresh" content="2">

<a href="{{ route('payment.cancel', [
            'paymentId' => $payment->id,
        ]) }}"
        class="ui button negative basic">
    @lang('general.cancel')
</a>
<a href="{{ \Request::getRequestUri() }}"
        class="ui button basic"
        title="@lang('misc.refresh')">
    @lang('misc.refresh')
</a>
