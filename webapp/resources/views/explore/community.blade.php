@extends('layouts.app')

@section('title', __('pages.explore.exploreCommunities'))

@php
    use \App\Http\Controllers\CommunityController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.bars'),
        'link' => route('explore.bar'),
        'icon' => 'beer',
    ];

    if(perms(CommunityController::permsCreate()))
        $menulinks[] = [
            'name' => __('pages.community.createCommunity'),
            'link' => route('community.create'),
            'icon' => 'plus',
        ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui two item menu">
        <a href="{{ route('explore.community') }}"
            class="item active">@lang('pages.communities')</a>
        <a href="{{ route('explore.bar') }}"
            class="item">@lang('pages.bars')</a>
    </div>

    @include('community.include.list')

    @if(perms(CommunityController::permsCreate()))
        <a href="{{ route('community.create') }}"
                class="ui button basic">
            @lang('pages.community.createCommunity')
        </a>
    @endif
@endsection
