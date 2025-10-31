{{-- TODO: provide $paymentable as well --}}

<div class="ui divider hidden"></div>

<a href="{{ route('payment.payRedirect', ['paymentId' => $payment->id]) }}" class="fluid ui huge button positive">
    @lang('barpay::payment.bunqmetab.continueToPayment')
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

{{-- If open parameter is set, automatically redirect and open to payment page --}}
@if($open)
    <script>setTimeout(() => { window.location = '{{ route('payment.payRedirect', ['paymentId' => $payment->id]) }}'; }, 1);</script>
@endif
