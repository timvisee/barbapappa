@extends('layouts.app')

@section('title', $bar->name)

@php
    use \App\Http\Controllers\BarMemberController;

    // Define menulinks
    if($page == 'info')
        $menulinks[] = [
            'name' => __('pages.bar.backToBar'),
            'link' => route('bar.show', ['barId' => $bar->human_id]),
            'icon' => 'undo',
        ];

    $menulinks[] = [
        'name' => __('pages.community.viewCommunity'),
        'link' => route('community.show', ['communityId' => $community->human_id]),
        'icon' => 'group',
    ];
@endphp

@section('content')
    @include('bar.include.barHeader')

    <div class="ui divider hidden"></div>

    <div class="ui two small statistics">
        @if(perms(BarMemberController::permsView()))
            <a href="{{ route('bar.member.index', ['barId' => $bar->human_id]) }}"
                    class="statistic">
                <div class="value">
                    {{ $bar->memberCount() }}
                </div>
                <div class="label">
                    @lang('misc.members')
                </div>
            </a>
        @else
            <div class="statistic">
                <div class="value">
                    {{ $bar->memberCount() }}
                </div>
                <div class="label">
                    @lang('misc.members')
                </div>
            </div>
        @endif
        <div class="statistic">
            <div class="value">
                @include('includes.humanTimeDiff', ['time' => $bar->created_at, 'short' => true, 'absolute' => true])
            </div>
            <div class="label">
                @lang('misc.active')
            </div>
        </div>
    </div>

    <div class="ui divider hidden"></div>

    <div class="ui segment">
        <p>
            @if(!empty($description = $bar->description()))
                {!! nl2br(e($description)) !!}
            @else
                <i>@lang('pages.bar.noDescription')...</i>
            @endif
        </p>
    </div>

    @include('bar.include.joinBanner')

    @if($page == 'info')
        <p>
            <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
                    class="ui button basic">
                @lang('pages.bar.backToBar')
            </a>
        </p>
    @endif
@endsection
