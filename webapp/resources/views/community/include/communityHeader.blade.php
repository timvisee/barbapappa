<h2 class="ui header">
    @if($joined)
        <a href="{{ route('community.leave', ['communityId' => $community->human_id]) }}"
                class="ui right pointing label green joined-label-popup"
                data-title="@lang('pages.community.joined')"
                data-content="@lang('pages.community.joinedClickToLeave')">
            <span class="halflings halflings-ok"></span>
        </a>
    @endif

    @yield('title')
</h2>
