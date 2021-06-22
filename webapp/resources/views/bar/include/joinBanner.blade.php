@if(!$joined && $bar->self_enroll)
    <div class="ui info message visible">
        <div class="header">@lang('pages.bar.notJoined')</div>
        <p>@lang('pages.bar.hintJoin')</p>
        <a href="{{ route('bar.join', ['barId' => $bar->human_id]) }}"
                class="ui button small positive basic">
            @lang('pages.bar.join')
        </a>
    </div>
@endif
