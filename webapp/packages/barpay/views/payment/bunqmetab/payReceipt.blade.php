{{-- TODO: provide $paymentable as well --}}

<div class="ui divider hidden"></div>

<a href="{{ url()->full() }}" class="ui loading button gray big">...</a>

<div class="ui left pointing label">
    @lang('barpay::payment.bunqmetab.processingDontPayTwice')
</div>

<div class="ui divider hidden"></div>

{{-- TODO: find a better refreshing method! --}}
<meta http-equiv="refresh" content="2">

<div class="ui info message">
    @lang('barpay::payment.bunqmetab.processingDescription')<br>
    <br>
    @lang('barpay::misc.mayClosePageWillNotify')
</div>

<div class="ui divider hidden"></div>

<a class="ui button primary"
        href="{{ route('dashboard') }}"
        title="@lang('pages.dashboard.title')">
    @lang('pages.dashboard.title')
</a>

<a href="{{ url()->full() }}"
        class="ui button basic"
        title="@lang('misc.refresh')">
    @lang('misc.refresh')
</a>

&nbsp;&nbsp;<a href="{{ url()->current() }}">@lang('barpay::misc.iHaveNotPaid')</a>
