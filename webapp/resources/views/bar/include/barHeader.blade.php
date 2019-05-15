<h2 class="ui header">
    @if($joined)
        <a href="{{ route('bar.leave', ['barId' => $bar->human_id]) }}"
                class="ui right pointing label green joined-label-popup"
                data-title="@lang('pages.bar.joined')"
                data-content="@lang('pages.bar.joinedClickToLeave')">
            <span class="halflings halflings-ok"></span>
        </a>
    @endif

    @yield('title')

    @if($joined)
        <a href="{{ route('community.wallet.list', [
                    'communityId' => $community->human_id,
                    'economyId' => $economy->id
                ]) }}">
            {!! $economy->formatBalance(BALANCE_FORMAT_LABEL) !!}
        </a>
    @endif
</h2>
