@extends('layouts.app')

@section('content')
    <h2 class="ui header">@lang('pages.bars')</h2>

    <div class="ui warning message visible">
        <div class="header">@lang('pages.bar.searchByCommunity')</div>
        <p>@lang('pages.bar.searchByCommunityDescription')</p>
        <a href="{{ route('community.overview') }}" class="ui button small basic">@lang('pages.community.viewCommunities')</a>
    </div>

    @include('bar.include.list')
@endsection
