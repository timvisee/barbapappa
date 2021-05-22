@extends('layouts.app')

@section('title', __('pages.communityMembers.title'))

@php
    use \App\Perms\CommunityRoles;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.community.backToCommunity'),
        'link' => route('community.manage', ['communityId' => $community->human_id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.communityMembers.description')</p>

    <div class="ui vertical menu fluid">
        {{--
            <div class="item">
                <div class="ui transparent icon input">
                    {{ Form::text('search', '', ['placeholder' => 'Search communities...']) }}
                    <i class="icon link">
                        <span class="glyphicons glyphicons-search"></span>
                    </i>
                </div>
            </div>
        --}}

        @forelse($members as $member)
            <a href="{{ route('community.member.show', [
                'communityId' => $community->human_id,
                'memberId' => $member->id,
            ]) }}" class="item">
                {{ $member->name }}
                @if($member->role != 0)
                    ({{ CommunityRoles::roleName($member->role) }})
                @endif
            </a>
        @empty
            <div class="item">
                <i>@lang('pages.communityMembers.noMembers')</i>
            </div>
        @endforelse
    </div>
    {{ $members->links() }}

    <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.backToCommunity')
    </a>
@endsection
