@if($joined)
    {{-- TODO: fix this doing nothing due to variable scoping --}}
    @php
        // Extend menu links
        $menulinks[] = [
            'name' => __('pages.community.leave'),
            'link' => route('community.leave', ['communityId' => $community->human_id]),
            'icon' => 'minus',
        ];
    @endphp

    <div class="ui success message visible">
        <div class="header">@lang('pages.community.joined')</div>
        <p>@lang('pages.community.youAreJoined')</p>
        <a href="{{ route('community.leave', ['communityId' => $community->human_id]) }}"
                class="ui button small basic">
            @lang('pages.community.leave')
        </a>
    </div>
@endif
