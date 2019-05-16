@extends('layouts.app')

@section('title', __('misc.managementHub'))

@php
    use \App\Http\Controllers\BarController;
    use \App\Http\Controllers\CommunityController;
    use \App\Http\Controllers\CommunityMemberController;
    use \App\Http\Controllers\EconomyController;

    // Define menulinks
    if(perms(CommunityController::permsUser()))
        $menulinks[] = [
            'name' => __('pages.community.editCommunity'),
            'link' => route('community.edit', ['communityId' => $community->human_id]),
            'icon' => 'edit',
        ];

    if(perms(EconomyController::permsView()))
        $menulinks[] = [
            'name' => __('pages.economies.title'),
            'link' => route('community.economy.index', ['communityId' => $community->human_id]),
            'icon' => 'money',
        ];

    if(perms(CommunityMemberController::permsView()))
        $menulinks[] = [
            'name' => __('misc.members'),
            'link' => route('community.member.index', ['communityId' => $community->human_id]),
            'icon' => 'user-structure',
        ];

    if(perms(BarController::permsCreate()))
        $menulinks[] = [
            'name' => __('pages.bar.createBar'),
            'link' => route('bar.create', ['communityId' => $community->human_id]),
            'icon' => 'plus',
        ];

    $menulinks[] = [
        'name' => __('pages.community.backToCommunity'),
        'link' => route('community.show', ['communityId' => $community->human_id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.for')
            <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}">
                {{ $community->name }}
            </a>
        </div>
    </h2>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.community')</h5>
        @if(perms(CommunityController::permsManage()))
            <a href="{{ route('community.edit', ['communityId' =>
            $community->human_id]) }}" class="item">
                @lang('pages.community.editCommunity')
            </a>
        @else
            <div class="item disabled">@lang('pages.community.editCommunity')</div>
        @endif
        {{-- TODO: add delete button to page links as well --}}
        <div class="item disabled">@lang('pages.community.deleteCommunity')</div>
    </div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">
            Assets
        </h5>
        @if(perms(EconomyController::permsView()))
            <a href="{{ route('community.economy.index', ['communityId' => $community->human_id]) }}" class="item">
                @lang('pages.economies.title')
            </a>
        @else
            <div class="item disabled">@lang('pages.economies.title')</div>
        @endif
        @if(perms(CommunityMemberController::permsView()))
            <a href="{{ route('community.member.index', ['communityId' => $community->human_id]) }}" class="item">
                @lang('misc.members')
            </a>
        @else
            <div class="item disabled">@lang('misc.members')</div>
        @endif
    </div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('pages.bars') ({{ $bars->count() }})</h5>
        @foreach($bars as $bar)
            {{-- TODO: do permission check, can user edit? --}}
            <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}" class="item">
                {{ $bar->name }}
            </a>
        @endforeach
        @if(perms(BarController::permsCreate()))
            <a href="{{ route('bar.create', ['communityId' => $community->human_id]) }}" class="ui bottom attached button">
                @lang('pages.bar.createBar')
            </a>
        @else
            <div class="ui bottom attached button disabled">@lang('pages.bar.createBar')</div>
        @endif
    </div>

    <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.backToCommunity')
    </a>
@endsection
