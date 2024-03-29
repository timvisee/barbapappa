@extends('layouts.app')

@section('title', empty($search) ? __('pages.communityMembers.title') : __('pages.communityMembers.search') . ': ' . $search)
@php
    $breadcrumbs = Breadcrumbs::generate('community.member.index', $community);
    $menusection = 'community_manage';

    use App\Perms\CommunityRoles;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.communityMembers.description')</p>

    <div class="ui vertical menu fluid">
        {!! Form::open(['method' => 'GET']) !!}
            <div class="item">
                <div class="ui transparent icon input">
                    {{ Form::search('q', Request::input('q'), ['placeholder' => __('pages.communityMembers.search') . '...']) }}
                    <i class="icon link">
                        <span class="glyphicons glyphicons-search"></span>
                    </i>
                </div>
            </div>
        {!! Form::close() !!}

        @forelse($members as $member)
            <a href="{{ route('community.member.show', [
                'communityId' => $community->human_id,
                'memberId' => $member->id,
            ]) }}" class="item">
                {{ $member->name }}
                @if($member->role != 0)
                    ({{ CommunityRoles::roleName($member->role) }})
                @endif

                @if($member->visited_at)
                    <span class="sub-label">
                        @include('includes.humanTimeDiff', ['time' => $member->visited_at])
                    </span>
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
