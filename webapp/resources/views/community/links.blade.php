@extends('layouts.app')

@section('title', __('pages.community.links.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.links', $community);
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.community.links.description')</p>

    <table class="ui compact celled definition table">
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
