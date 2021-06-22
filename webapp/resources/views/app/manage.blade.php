@extends('layouts.app')

@section('title', __('misc.managementHub'))
@php
    $breadcrumbs = Breadcrumbs::generate('app.manage');
    $menusection = 'app_manage';

    use App\Http\Controllers\AppBunqAccountController;
    use App\Http\Controllers\CommunityController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.assets')</h5>
        @if(perms(AppBunqAccountController::permsView()))
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
