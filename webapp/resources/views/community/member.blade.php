@extends('layouts.app')

@section('title', __('pages.communityMember.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.member', $community);
    $menusection = 'community';

    use \App\Http\Controllers\CommunityMemberController;
@endphp

@section('content')
    <div class="ui success message visible">
        <div class="header">@lang('pages.community.joined')</div>
        <p>@lang('pages.community.youAreJoined')</p>
        <a href="{{ route('community.leave', ['communityId' => $community->human_id]) }}"
                class="ui button small basic">
            @lang('pages.community.leave')
        </a>
    </div>

    <p>
        <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
                class="ui button basic">
            @lang('pages.community.backToCommunity')
        </a>
    </p>
@endsection
