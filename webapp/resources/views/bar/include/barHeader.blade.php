<h2 class="ui header bar-header">
    <div>
        @if($joined)
            <a href="{{ route('bar.leave', ['barId' => $bar->human_id]) }}"
                    class="ui right pointing label green joined-label-popup"
                    data-title="@lang('pages.bar.joined')"
                    data-content="@lang('pages.bar.joinedClickToLeave')">
                <span class="halflings halflings-ok"></span>
            </a>
        @endif

        @yield('title')
    </div>

    @if($joined || $economy->userHasBalance())
        <div>
            @php
                $balance = $economy->calcUserBalance();
            @endphp

            {{-- Quick top-up if user has negative balance --}}
            @if($balance->amount < 0)
                <a href="{{ route('community.wallet.topUpEconomy', [
                            'communityId' => $community->human_id,
                            'economyId' => $economy->id
                        ]) }}"
                        class="ui right pointing label red label-top-up"
                        data-title="@lang('pages.bar.joined')"
                        data-content="@lang('pages.bar.joinedClickToLeave')">
                    @lang('pages.wallets.topUpNow')
                </a>
            @endif

            {{-- Balance label --}}
            <a href="{{ route('community.wallet.list', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                    ]) }}"
                    class="balance">
                {!! $balance->formatAmount(BALANCE_FORMAT_LABEL) !!}
            </a>
        </div>
    @endif


</h2>
