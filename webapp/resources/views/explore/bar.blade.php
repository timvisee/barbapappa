@extends('layouts.app')

@section('title', __('pages.explore.exploreBars'))

@php
    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.communities'),
        'link' => route('explore.community'),
        'icon' => 'group',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui two item menu">
        <a href="{{ route('explore.community') }}"
            class="item">@lang('pages.communities')</a>
        <a href="{{ route('explore.bar') }}"
            class="item active">@lang('pages.bars')</a>
    </div>

    <div class="ui info message visible">
        <div class="header">@lang('pages.bar.searchByCommunity')</div>
        <p>@lang('pages.bar.searchByCommunityDescription')</p>
        <a href="{{ route('explore.community') }}" class="ui button basic">
            @lang('pages.explore.exploreCommunities')
        </a>
    </div>

    @include('bar.include.list')
@endsection
