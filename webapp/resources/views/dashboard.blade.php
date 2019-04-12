@extends('layouts.app')

@section('title', __('pages.dashboard'))

@section('content')
    <h3 class="ui header">@lang('pages.bar.yourBars')</h3>
    @include('bar.include.list')

    <h3 class="ui header">@lang('pages.community.yourCommunities')</h3>
    @include('community.include.list')
@endsection
