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
            <a href="{{ route('community.wallet.list', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id
                    ]) }}"
                    class="balance">
                {!! $economy->formatBalance(BALANCE_FORMAT_LABEL) !!}
            </a>
        </div>
    @endif
</h2>
