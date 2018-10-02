@extends('layouts.app')

@section('content')
    <h2 class="ui header">
        @if($joined)
            <a href="{{ route('bar.leave', ['barId' => $bar->human_id]) }}"
                    class="ui right pointing label green joined-label-popup"
                    data-title="@lang('pages.bar.joined')"
                    data-content="@lang('pages.bar.joinedClickToLeave')">
                <span class="halflings halflings-ok"></span>
            </a>
        @endif

        {{ $bar->name }}
    </h2>

    @unless($joined)
        <div class="ui info message visible">
            <div class="header">@lang('pages.bar.notJoined')</div>
            <p>@lang('pages.bar.hintJoin')</p>
            <a href="{{ route('bar.join', ['barId' => $bar->human_id]) }}"
                    class="ui button small positive basic">
                @lang('pages.bar.join')
            </a>
        </div>
    @endif

    <div class="ui vertical menu fluid">
        <div class="item">Product 1</div>
        <div class="item">Product 2</div>
        <div class="item">Product 3</div>
        <div class="item">Product 4</div>
        <div class="item">Product 5</div>
    </div>

    <div class="ui section divider"></div>

    <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
            class="ui button small basic">
        @lang('pages.community.viewCommunity')
    </a>

    {{-- TODO only show if the user has permission --}}
    <a href="{{ route('bar.edit', ['barId' => $bar->human_id]) }}"
            class="ui button small basic">
        @lang('pages.bar.editBar')
    </a>
@endsection
