@extends('layouts.app')

@section('title', $community->name)

@php
    use \App\Http\Controllers\BarController;
    use \App\Http\Controllers\CommunityController;
    use \App\Http\Controllers\CommunityMemberController;
    use \App\Http\Controllers\EconomyController;

    // Define menulinks
    if(perms(CommunityController::permsUser())) {
        $menulinks[] = [
            'name' => __('pages.community.communityInfo'),
            'link' => route('community.info', ['communityId' => $community->human_id]),
            'icon' => 'info-sign',
        ];
        $menulinks[] = [
            'name' => __('pages.stats.title'),
            'link' => route('community.stats', ['communityId' => $community->human_id]),
            'icon' => 'stats',
        ];
    }

    if(perms(CommunityMemberController::permsView()))
        $menulinks[] = [
            'name' => __('pages.communityMembers.title'),
            'link' => route('community.member.index', ['communityId' => $community->human_id]),
            'icon' => 'user-structure',
        ];

    if(perms(EconomyController::permsView()))
        $menulinks[] = [
            'name' => __('pages.economies.title'),
            'link' => route('community.economy.index', ['communityId' => $community->human_id]),
            'icon' => 'money',
        ];

    if($joined)
        $menulinks[] = [
            'name' => __('pages.wallets.yourWallets'),
            'link' => route('community.wallet.index', ['communityId' => $community->human_id]),
            'icon' => 'wallet',
        ];

    if(perms(CommunityController::permsManage()))
        $menulinks[] = [
            'name' => __('pages.community.editCommunity'),
            'link' => route('community.edit', ['communityId' => $community->human_id]),
            'icon' => 'edit',
        ];

    if(perms(BarController::permsCreate()))
        $menulinks[] = [
            'name' => __('pages.bar.createBar'),
            'link' => route('bar.create', ['communityId' => $community->human_id]),
            'icon' => 'plus',
        ];
@endphp

@section('content')
    @include('community.include.communityHeader')
    @include('community.include.joinBanner')

    <h3 class="ui header">@lang('pages.bars')</h3>
    @include('bar.include.list')

    @if(perms(BarController::permsCreate()))
        <a href="{{ route('bar.create', ['communityId' => $community->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.createBar')
        </a>
    @endif
@endsection
