@extends('layouts.app')

@section('title', __('pages.community.links.title'))

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.for')
            <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}">
                {{ $community->name }}
            </a>
        </div>
    </h2>
    <p>@lang('pages.community.links.description')</p>

    <table class="ui compact celled definition table bottom attached">
        <tbody>
            <tr>
                <td>@lang('pages.community.links.linkCommunity')</td>
                <td><code class="literal copy">{{ route('community.show', ['communityId' => $community->human_id]) }}</code></td>
            </tr>
            @if($community->self_enroll)
                <tr>
                    <td>@lang('pages.community.links.linkJoinCommunity')</td>
                    <td><code class="literal copy">{{ route('community.join', ['communityId' => $community->human_id]) }}</code></td>
                </tr>
            @endif
            @if($community->self_enroll && $community->password)
                <tr>
                    <td>@lang('pages.community.links.linkJoinCommunityCode')</td>
                    <td><code class="literal copy">{{ route('community.join', ['communityId' => $community->human_id, 'code' => $community->password]) }}</code></td>
                </tr>
            @endif
        </tbody>
    </table>

    <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.backToCommunity')
    </a>
@endsection
