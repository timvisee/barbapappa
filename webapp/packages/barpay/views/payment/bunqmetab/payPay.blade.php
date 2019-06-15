{{-- TODO: provide $paymentable as well --}}

<div class="ui divider hidden"></div>

<a href="{{ $bunq_tab_url }}" class="ui button primary big">
    @lang('barpay::misc.pay')
</a>

<div class="ui divider hidden"></div>

<div class="ui info message">
    @lang('barpay::payment.bunqmetab.pleasePay')<br>
    <br>
    @lang('barpay::payment.bunqmetab.handledByBunq')
</div>

<div class="ui divider hidden"></div>

<a href="{{ url()->current() }}"
        class="ui button basic"
        title="@lang('misc.refresh')">
    @lang('misc.refresh')
</a>
<a href="{{ route('payment.cancel', [
            'paymentId' => $payment->id,
        ]) }}"
        class="ui button negative basic">
    @lang('general.cancel')
</a>
