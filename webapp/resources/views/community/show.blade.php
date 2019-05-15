@extends('layouts.app')

@section('title', $community->name)

@php
    use \App\Http\Controllers\BarController;
    use \App\Http\Controllers\CommunityController;
    use \App\Http\Controllers\CommunityMemberController;
    use \App\Http\Controllers\EconomyController;
@endphp

@section('content')
    @include('community.include.communityHeader')
    @include('community.include.joinBanner')

    <h3 class="ui header">@lang('pages.bars')</h3>
    @include('bar.include.list')

    <br />

    @if(perms(CommunityController::permsUser()))
        <a href="{{ route('community.info', ['communityId' => $community->human_id]) }}"
                class="ui button basic">
            @lang('pages.community.communityInfo')
        </a>
    @endif

    @if(perms(CommunityMemberController::permsView()))
        <a href="{{ route('community.member.index', ['communityId' => $community->human_id]) }}"
                class="ui button basic">
            @lang('pages.communityMembers.title')
        </a>
    @endif

    @if(perms(EconomyController::permsView()))
        <a href="{{ route('community.economy.index', ['communityId' => $community->human_id]) }}"
                class="ui button basic">
            @lang('pages.economies.title')
        </a>
    @endif

    <a href="{{ route('community.wallet.index', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.wallets.yourWallets')
    </a>

    @if(perms(CommunityController::permsManage()))
        <a href="{{ route('community.edit', ['communityId' => $community->human_id]) }}"
                class="ui button basic">
            @lang('pages.community.editCommunity')
        </a>
    @endif

    @if(perms(BarController::permsCreate()))
        <a href="{{ route('bar.create', ['communityId' => $community->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.createBar')
        </a>
    @endif
@endsection
