@extends('layouts.app')

@section('content')
    <h2 class="ui header">
        @if($joined)
            <a href="{{ route('bar.leave', ['barId' => $bar->id]) }}"
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
            <a href="{{ route('bar.join', ['barId' => $bar->id]) }}"
                    class="ui button small positive basic">
                @lang('pages.bar.join')
            </a>
        </div>
    @endif

    <p>[TODO show bar info]</p>
@endsection
