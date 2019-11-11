@extends('layouts.app')

@section('title', __('pages.finance.title'))

@php
    use \App\Http\Controllers\FinanceController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.economies.backToEconomy'),
        'link' => route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.economy.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                    ]) }}">
                {{ $economy->name }}
            </a>
        </div>
    </h2>

    {{--<div class="ui vertical menu fluid">--}}
    {{--    {1{----}}
    {{--        <div class="item">--}}
    {{--            <div class="ui transparent icon input">--}}
    {{--                {{ Form::text('search', '', ['placeholder' => 'Search bars...']) }}--}}
    {{--                <i class="icon glyphicons glyphicons-search link"></i>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    --}1}--}}

    {{--    @forelse($members as $member)--}}
    {{--        <a href="{{ route('bar.member.show', [--}}
    {{--            'barId' => $bar->human_id,--}}
    {{--            'memberId' => $member->id,--}}
    {{--        ]) }}" class="item">--}}
    {{--            {{ $member->name }}--}}
    {{--            @if($member->role != 0)--}}
    {{--                ({{ BarRoles::roleName($member->role) }})--}}
    {{--            @endif--}}
    {{--        </a>--}}
    {{--    @empty--}}
    {{--        <div class="item">--}}
    {{--            <i>@lang('pages.barMembers.noMembers')</i>--}}
    {{--        </div>--}}
    {{--    @endforelse--}}
    {{--</div>--}}

    <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
            class="ui button basic">
        @lang('pages.economies.backToEconomy')
    </a>
@endsection
