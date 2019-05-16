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
            'name' => __('misc.information'),
            'link' => route('community.info', ['communityId' => $community->human_id]),
            'icon' => 'info-sign',
        ];
        $menulinks[] = [
            'name' => __('pages.stats.title'),
            'link' => route('community.stats', ['communityId' => $community->human_id]),
            'icon' => 'stats',
        ];
    }

    if($joined)
        $menulinks[] = [
            'name' => __('pages.wallets.yourWallets'),
            'link' => route('community.wallet.index', ['communityId' => $community->human_id]),
            'icon' => 'wallet',
        ];

    if(perms(CommunityController::permsManage()))
        $menulinks[] = [
            'name' => 'Manage', // __('pages.bar.createBar'),
            'link' => route('community.manage', ['communityId' => $community->human_id]),
            'icon' => 'edit',
        ];
@endphp

@section('content')
    @include('community.include.communityHeader')
    @include('community.include.joinBanner')

    <h3 class="ui header">@lang('pages.bars')</h3>
    @include('bar.include.list')
@endsection
