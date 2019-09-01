@extends('layouts.app')

@section('title', __('pages.communityMembers.title'))

@php
    use \App\Perms\CommunityRoles;

    // Get all community members
    $members = $community->members(['role'])->get();

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.community.backToCommunity'),
        'link' => route('community.manage', ['communityId' => $community->human_id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title') ({{ count($members) }})</h2>
    <p>@lang('pages.communityMembers.description')</p>

    <div class="ui vertical menu fluid">
        {{--
            <div class="item">
                <div class="ui transparent icon input">
                    {{ Form::text('search', '', ['placeholder' => 'Search communities...']) }}
                    <i class="icon glyphicons glyphicons-search link"></i>
                </div>
            </div>
        --}}

        @forelse($members as $member)
            <a href="{{ route('community.member.show', ['communityId' => $community->human_id, 'memberId' => $member->id]) }}" class="item">
                {{ $member->name }}
                @if($member->pivot->role != 0)
                    ({{ CommunityRoles::roleName($member->pivot->role) }})
                @endif
            </a>
        @empty
            <div class="item">
                <i>@lang('pages.communityMembers.noMembers')</i>
            </div>
        @endforelse
    </div>

    <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.backToCommunity')
    </a>
@endsection
