{{-- TODO: link wallet and transaction related to this payment --}}

<div class="ui divider hidden"></div>

<div class="ui one tiny statistics">
    <div class="statistic">
        <div class="label">
            Waiting for
        </div>
        <div class="value">
            {{ $timeWaiting }}
        </div>
    </div>
</div>

<div class="ui divider hidden"></div>

<div class="ui info message">
    {{-- TODO: translate --}}
    Waiting for a community manager to manually confirm your transaction has
    been received. This may take a long while.<br>
    <br>
    You may close this page now. You will be notified by email when the status
    of this payment changes.
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
