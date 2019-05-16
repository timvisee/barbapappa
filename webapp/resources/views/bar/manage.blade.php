@extends('layouts.app')

@section('title', __('misc.managementHub'))

@php
    use \App\Http\Controllers\BarController;
    use \App\Http\Controllers\BarMemberController;
    use \App\Http\Controllers\CommunityController;

    // Define menulinks
    if(perms(BarController::permsAdminister()))
        $menulinks[] = [
            'name' => __('pages.bar.editBar'),
            'link' => route('bar.edit', ['barId' => $bar->human_id]),
            'icon' => 'edit',
        ];

    // TODO: edit products
    // if(perms(EconomyController::permsView()))
    //     $menulinks[] = [
    //         'name' => __('pages.economies.title'),
    //         'link' => route('bar.economy.index', ['barId' => $bar->human_id]),
    //         'icon' => 'money',
    //     ];

    if(perms(BarMemberController::permsView()))
        $menulinks[] = [
            'name' => __('misc.members'),
            'link' => route('bar.member.index', ['barId' => $bar->human_id]),
            'icon' => 'user-structure',
        ];

    if(perms(CommunityController::permsManage()))
        $menulinks[] = [
            'name' => __('pages.community.manageCommunity'),
            'link' => route('community.manage', ['communityId' => $bar->community->human_id]),
            'icon' => 'group',
        ];

    $menulinks[] = [
        'name' => __('pages.bar.backToBar'),
        'link' => route('bar.show', ['barId' => $bar->human_id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.for')
            <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}">
                {{ $bar->name }}
            </a>
        </div>
    </h2>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.bar')</h5>
        @if(perms(BarController::permsAdminister()))
            <a href="{{ route('bar.edit', ['barId' =>
            $bar->human_id]) }}" class="item">
                @lang('pages.bar.editBar')
            </a>
        @else
            <div class="item disabled">@lang('pages.bar.editBar')</div>
        @endif
        @if(perms(BarController::permsAdminister()))
            {{-- TODO: add delete button to page links as well --}}
            {{-- <a href="{{ route('bar.delete', ['barId' => --}}
            {{-- $bar->human_id]) }}" class="item"> --}}
            {{--     @lang('pages.bar.deleteBar') --}}
            {{-- </a> --}}
            <div class="item disabled">@lang('pages.bar.deleteBar')</div>
        @else
            <div class="item disabled">@lang('pages.bar.deleteBar')</div>
        @endif
    </div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.assets')</h5>
        {{-- TODO: products here --}}
        @if(perms(BarMemberController::permsView()))
            <a href="{{ route('bar.member.index', ['barId' => $bar->human_id]) }}" class="item">
                @lang('misc.members')
            </a>
        @else
            <div class="item disabled">@lang('misc.members')</div>
        @endif
    </div>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.community')</h5>
        @if(perms(CommunityController::permsManage()))
            <a href="{{ route('community.manage', ['communityId' => $bar->community->human_id]) }}" class="item">
                @lang('pages.community.manageCommunity')
            </a>
        @else
            <div class="item disabled">@lang('pages.community.manageCommunity')</div>
        @endif
    </div>

    <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
            class="ui button basic">
        @lang('pages.bar.backToBar')
    </a>
@endsection
