@extends('layouts.app')

@section('title', __('pages.explore.exploreCommunities'))
@php
    $breadcrumbs = Breadcrumbs::generate('explore');

    use App\Http\Controllers\CommunityController;
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

    {{ $communities->links() }}
@endsection
