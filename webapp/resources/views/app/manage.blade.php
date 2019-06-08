@extends('layouts.app')

@section('title', __('misc.managementHub'))

@php
    use App\Http\Controllers\CommunityController;
    // // Define menulinks
    // if(perms(CommunityController::permsAdminister())) {
    //     $menulinks[] = [
    //         'name' => __('pages.community.editCommunity'),
    //         'link' => route('community.edit', ['communityId' => $community->human_id]),
    //         'icon' => 'edit',
    //     ];
    //     $menulinks[] = [
    //         'name' => __('pages.community.deleteCommunity'),
    //         'link' => route('community.delete', ['communityId' => $community->human_id]),
    //         'icon' => 'delete',
    //     ];
    // }
    // 
    // if(perms(EconomyController::permsView()))
    //     $menulinks[] = [
    //         'name' => __('pages.economies.title'),
    //         'link' => route('community.economy.index', ['communityId' => $community->human_id]),
    //         'icon' => 'money',
    //     ];
    // 
    // if(perms(CommunityMemberController::permsView()))
    //     $menulinks[] = [
    //         'name' => __('misc.members'),
    //         'link' => route('community.member.index', ['communityId' => $community->human_id]),
    //         'icon' => 'user-structure',
    //     ];
    // 
    // if(perms(CommunityController::permsManage()))
    //     $menulinks[] = [
    //         'name' => __('pages.community.generatePoster'),
    //         'link' => route('community.poster.generate', ['communityId' => $community->human_id]),
    //         'icon' => 'qrcode',
    //     ];
    // 
    // if(perms(BarController::permsCreate()))
    //     $menulinks[] = [
    //         'name' => __('pages.bar.createBar'),
    //         'link' => route('bar.create', ['communityId' => $community->human_id]),
    //         'icon' => 'plus',
    //     ];
    // 
    // $menulinks[] = [
    //     'name' => __('pages.community.backToCommunity'),
    //     'link' => route('community.show', ['communityId' => $community->human_id]),
    //     'icon' => 'undo',
    // ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.for')
            <a href="{{ route('about') }}">
                {{ config('app.name') }}
            </a>
        </div>
    </h2>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('pages.communities') ({{ $communities->count() }})</h5>
        @foreach($communities as $community)
            <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}" class="item">
                {{ $community->name }}
            </a>
        @endforeach
        @if(perms(CommunityController::permsCreate()))
            <a href="{{ route('community.create') }}" class="ui bottom attached button">
                @lang('pages.community.createCommunity')
            </a>
        @else
            <div class="ui bottom attached button disabled">@lang('pages.community.createCommunity')</div>
        @endif
    </div>

    <a href="{{ route('dashboard') }}"
            class="ui button basic">
        @lang('pages.dashboard.title')
    </a>
@endsection
