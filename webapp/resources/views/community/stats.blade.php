@extends('layouts.app')

@section('title', __('pages.stats.communityStats'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.stats', $community);
    $menusection = 'community';

    use \App\Http\Controllers\CommunityMemberController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <h3 class="ui horizontal divider header">@lang('misc.members')</h3>
    <div class="ui one small statistics">
        @if(perms(CommunityMemberController::permsView()))
            <a href="{{ route('community.member.index', ['communityId' => $community->human_id]) }}"
                    class="statistic">
                <div class="value">{{ $community->memberCount() }}</div>
                <div class="label">@lang('misc.enrolled')</div>
            </a>
        @else
            <div class="statistic">
                <div class="value">{{ $community->memberCount() }}</div>
                <div class="label">@lang('misc.members')</div>
            </div>
        @endif
    </div>
    <div class="ui horizontal small statistics">
        <div class="statistic">
            <div class="value">{{ $memberCountHour }}</div>
            <div class="label">@lang('pages.stats.activePastHour')</div>
        </div>
        <div class="statistic">
            <div class="value">{{ $memberCountDay }}</div>
            <div class="label">@lang('pages.stats.activePastDay')</div>
        </div>
        <div class="statistic">
            <div class="value">{{ $memberCountMonth }}</div>
            <div class="label">@lang('pages.stats.activePastMonth')</div>
        </div>
    </div>

    <h3 class="ui horizontal divider header">@lang('misc.community')</h3>
    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                @include('includes.humanTimeDiff', ['time' => $community->created_at, 'short' => true, 'absolute' => true])
            </div>
            <div class="label">@lang('misc.active')</div>
        </div>
    </div>

    <div class="ui divider hidden"></div>

    <p>
        <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
                class="ui button basic">
            @lang('pages.community.backToCommunity')
        </a>
    </p>
@endsection
