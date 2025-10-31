<div class="ui divider hidden"></div>
<div class="ui hidden divider"></div>
<div class="ui hidden divider"></div>

<div class="ui active centered large inline loader"></div>

<div class="ui hidden divider"></div>

<p class="align-center">
    @if(($secondsPassed = $payment->created_at?->diffInSeconds()) < 6)
        @lang('barpay::misc.justASecond')
    @elseif($secondsPassed < 60)
        @lang('barpay::misc.takingSomeTime')
    @else
        @lang('barpay::misc.tryAgainLater')
    @endif
</p>

<div class="ui hidden divider"></div>
<div class="ui hidden divider"></div>
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
