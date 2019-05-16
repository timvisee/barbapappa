@if(!$joined)
    {{-- TODO: fix this doing nothing due to variable scoping --}}
    @php
        // Extend menu links
        $menulinks[] = [
            'name' => __('pages.bar.join'),
            'link' => route('bar.join', ['barId' => $bar->human_id]),
            'icon' => 'plus',
        ];
    @endphp

    <div class="ui info message visible">
        <div class="header">@lang('pages.bar.notJoined')</div>
        <p>@lang('pages.bar.hintJoin')</p>
        <a href="{{ route('bar.join', ['barId' => $bar->human_id]) }}"
                class="ui button small positive basic">
            @lang('pages.bar.join')
        </a>
    </div>
@endif
