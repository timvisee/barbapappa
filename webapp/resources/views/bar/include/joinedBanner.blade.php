@if($joined)
    {{-- TODO: fix this doing nothing due to variable scoping --}}
    @php
        // Extend menu links
        $menulinks[] = [
            'name' => __('pages.bar.leave'),
            'link' => route('bar.leave', ['barId' => $bar->human_id]),
            'icon' => 'minus',
        ];
    @endphp

    <div class="ui success message visible">
        <div class="header">@lang('pages.bar.joined')</div>
        <p>@lang('pages.bar.youAreJoined')</p>
        <a href="{{ route('bar.leave', ['barId' => $bar->human_id]) }}" class="ui button basic">
            @lang('pages.bar.leave')
        </a>
    </div>
@endif
