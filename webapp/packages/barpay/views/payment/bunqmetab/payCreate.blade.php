<div class="ui divider hidden"></div>

<a href="{{ url()->current() }}" class="ui loading button primary big">...</a>

<div class="ui left pointing blue label">
    @lang('barpay::misc.justASecond')
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
<a href="{{ url()->current() }}"
        class="ui button basic"
        title="@lang('misc.refresh')">
    @lang('misc.refresh')
</a>
