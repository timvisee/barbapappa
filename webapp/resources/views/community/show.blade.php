@extends('layouts.app')

@section('title', $community->name)
@php
    $breadcrumbs = Breadcrumbs::generate('community.show', $community);
@endphp

@php
    use \App\Http\Controllers\BarController;
    use \App\Http\Controllers\CommunityController;
    use \App\Http\Controllers\CommunityMemberController;
    use \App\Http\Controllers\EconomyController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('misc.information'),
        'link' => route('community.info', ['communityId' => $community->human_id]),
        'icon' => 'info-sign',
    ];

    if(perms(CommunityController::permsUser()))
        $menulinks[] = [
            'name' => __('pages.stats.title'),
            'link' => route('community.stats', ['communityId' => $community->human_id]),
            'icon' => 'stats',
        ];

    if($joined)
        $menulinks[] = [
            'name' => __('pages.wallets.myWallets'),
            'link' => route('community.wallet.index', ['communityId' => $community->human_id]),
            'icon' => 'wallet',
        ];

    if(perms(CommunityController::permsManage()))
        $menulinks[] = [
            'name' => __('misc.manage'),
            'link' => route('community.manage', ['communityId' => $community->human_id]),
            'icon' => 'edit',
        ];
@endphp

@section('content')
    @include('community.include.communityHeader')
    @include('community.include.joinBanner')

    @include('bar.include.list', [
        'header' => __('pages.bars') . ' (' . count($bars) . ')',
    ])
@endsection
