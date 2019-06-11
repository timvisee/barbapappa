@extends('layouts.app')

@section('title', __('misc.managementHub'))

@php
    use App\Http\Controllers\BunqAccountController;
    use App\Http\Controllers\CommunityController;

    // Define menulinks
    if(perms(BunqAccountController::permsView()))
        $menulinks[] = [
            'name' => __('pages.bunqAccounts.title'),
            'link' => route('app.bunqAccount.index'),
            'icon' => 'credit-card',
        ];
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
        <h5 class="ui item header">@lang('misc.assets')</h5>
        @if(perms(BunqAccountController::permsView()))
            <a href="{{ route('app.bunqAccount.index') }}" class="item">
                @lang('pages.bunqAccounts.title')
            </a>
        @else
            <div class="item disabled">@lang('pages.economies.title')</div>
        @endif
    </div>

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
