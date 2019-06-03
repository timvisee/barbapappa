{{-- TODO: link wallet and transaction related to this payment --}}

<div class="ui divider hidden"></div>

<div class="ui one tiny statistics">
    <div class="statistic">
        <div class="label">
            @lang('misc.continueIn')
        </div>
        <div class="value">
            {{ $timeLeft }}
        </div>
    </div>
</div>

<div class="ui divider hidden"></div>

<div class="ui info message">
    @lang('barpay::payment.manualiban.waitOnTransfer')<br>
    <br>
    @lang('barpay::misc.mayClosePageWillNotify')
</div>

<div class="ui divider hidden"></div>

<a class="ui button primary"
        href="{{ route('dashboard') }}"
        title="@lang('pages.dashboard.title')">
    @lang('pages.dashboard.title')
</a>

<a href="{{ url()->current() }}"
        class="ui button basic"
        title="@lang('misc.refresh')">
    @lang('misc.refresh')
</a>
