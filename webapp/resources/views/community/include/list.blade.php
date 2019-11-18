<div class="ui vertical menu fluid">
    @if(isset($header))
        <h5 class="ui item header">{{ $header }}</h5>
    @endif

    {{--
        <div class="item">
            <div class="ui transparent icon input">
                {{ Form::text('search', '', ['placeholder' => 'Search communities...']) }}
                <i class="icon link">
                    <span class="glyphicons glyphicons-search"></span>
                </i>
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
