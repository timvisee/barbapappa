@if($joined)
    <div class="ui success message visible">
        <div class="header">@lang('pages.bar.joined')</div>
        <p>@lang('pages.bar.youAreJoined')</p>
        <a href="{{ route('bar.leave', ['barId' => $bar->human_id]) }}" class="ui button basic">
            @lang('pages.bar.leave')
        </a>
    </div>
@endif
