<div class="ui vertical menu fluid">
    {{--
        <div class="item">
            <div class="ui transparent icon input">
                <input type="text" placeholder="Search communities...">
                <i class="icon glyphicons glyphicons-search link"></i>
            </div>
        </div>
    --}}

    @forelse($communities as $community)
        <a href="{{ route('community.show', ['communityId' => $community->human_id ]) }}" class="item">
            {{ $community->name }}
        </a>
    @empty
        <div class="item">
            <i>@lang('pages.community.noCommunities')</i>
        </div>
    @endforelse
</div>
