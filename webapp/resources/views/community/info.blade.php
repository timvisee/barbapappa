@extends('layouts.app')

@section('title', $community->name)

@php
    use \App\Http\Controllers\CommunityMemberController;

    // Define menulinks
    if($page == 'info')
        $menulinks[] = [
            'name' => __('pages.community.backToCommunity'),
            'link' => route('community.show', ['communityId' => $community->human_id]),
            'icon' => 'undo',
        ];
@endphp

@section('content')
    @include('community.include.communityHeader')

    <div class="ui divider hidden"></div>

    <div class="ui two small statistics">
        @if(perms(CommunityMemberController::permsView()))
            <a href="{{ route('community.member.index', ['communityId' => $community->human_id]) }}"
                    class="statistic">
                <div class="value">
                    {{ $community->memberCount() }}
                </div>
                <div class="label">
                    @lang('misc.members')
                </div>
            </a>
        @else
            <div class="statistic">
                <div class="value">
                    {{ $community->memberCount() }}
                </div>
                <div class="label">
                    @lang('misc.members')
                </div>
            </div>
        @endif
        <div class="statistic">
            <div class="value">
                @include('includes.humanTimeDiff', ['time' => $community->created_at, 'short' => true, 'absolute' => true])
            </div>
            <div class="label">
                @lang('misc.active')
            </div>
        </div>
    </div>

    <div class="ui divider hidden"></div>

    <div class="ui segment">
        <p>
            @if(!empty($description = $community->description()))
                {!! nl2br(e($description)) !!}
            @else
                <i>@lang('pages.community.noDescription')...</i>
            @endif
        </p>
    </div>

    @include('community.include.joinBanner')
    @include('community.include.joinedBanner')

    @if($page == 'info')
        <p>
            <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
                    class="ui button basic">
                @lang('pages.community.backToCommunity')
            </a>
        </p>
    @endif
@endsection
