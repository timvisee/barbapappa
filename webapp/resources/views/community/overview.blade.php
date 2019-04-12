@extends('layouts.app')

@section('title', __('pages.communities'))

@php
    use \App\Http\Controllers\CommunityController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    @include('community.include.list')

    @if(perms(CommunityController::permsCreate()))
        <br />

        <a href="{{ route('community.create') }}"
                class="ui button basic">
            @lang('pages.community.createCommunity')
        </a>
    @endif
@endsection
