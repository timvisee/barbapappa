@if(!$joined && $community->self_enroll)
    {{-- TODO: fix this doing nothing due to variable scoping --}}
    @php
        // Extend menu links
        $menulinks[] = [
            'name' => __('pages.community.join'),
            'link' => route('community.join', ['communityId' => $community->human_id]),
            'icon' => 'plus',
        ];
    @endphp

    <div class="ui info message visible">
        <div class="header">@lang('pages.community.notJoined')</div>
        <p>@lang('pages.community.hintJoin')</p>
        <a href="{{ route('community.join', ['communityId' => $community->human_id]) }}"
                class="ui button small positive basic">
            @lang('pages.community.join')
        </a>
    </div>
@endif
