@unless($joined)
    <div class="ui info message visible">
        <div class="header">@lang('pages.community.notJoined')</div>
        <p>@lang('pages.community.hintJoin')</p>
        <a href="{{ route('community.join', ['communityId' => $community->human_id]) }}"
                class="ui button small positive basic">
            @lang('pages.community.join')
        </a>
    </div>
@endif
