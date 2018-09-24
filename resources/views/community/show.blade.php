@extends('layouts.app')

@section('content')
    <h2 class="ui header">
        @if($joined)
            <a href="{{ route('community.leave', ['communityId' => $community->id]) }}"
                    class="ui right pointing label green joined-label-popup"
                    data-title="@lang('pages.community.joined')"
                    data-content="@lang('pages.community.joinedClickToLeave')">
                <span class="halflings halflings-ok"></span>
            </a>
        @endif

        {{ $community->name }}
    </h2>

    @unless($joined)
        <div class="ui info message visible">
            <div class="header">@lang('pages.community.notJoined')</div>
            <p>@lang('pages.community.hintJoin')</p>
            <a href="{{ route('community.join', ['communityId' => $community->id]) }}"
                    class="ui button small positive basic">
                @lang('pages.community.join')
            </a>
        </div>
    @endif

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>ID</td>
                <td><a href="{{ route('community.show', ['communityId' => $community->id]) }}">{{ $community->id }}</a></td>
            </tr>
            <tr>
                <td>Name</td>
                <td>{{ $community->name }}</td>
            </tr>
            <tr>
                <td>Joined</td>
                <td>{{ $joined ? "Yes" : "No" }}</td>
            </tr>
            <tr>
                <td>Slug</td>
                @if($community->hasSlug())
                    <td><a href="{{ route('community.show', ['communityId' => $community->slug]) }}">{{ $community->slug }}</a></td>
                @else
                    <td><i>None</i></td>
                @endif
            </tr>
        </tbody>
    </table>

    <h3 class="ui header">@lang('pages.bars')</h3>
    @include('bar.include.list')
@endsection
