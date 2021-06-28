@extends('layouts.app')

@section('title', __('pages.barMember.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('bar.member', $bar);
    $menusection = 'bar';

    use \App\Http\Controllers\BarMemberController;
@endphp

@section('content')
    {{-- Joined, leave button --}}
    <div class="ui success message visible">
        <div class="header">@lang('pages.bar.joined')</div>
        <p>@lang('pages.bar.youAreJoined')</p>
        <a href="{{ route('bar.leave', ['barId' => $bar->human_id]) }}" class="ui button basic">
            @lang('pages.bar.leave')
        </a>
    </div>

    <p>
        <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.backToBar')
        </a>
    </p>
@endsection
