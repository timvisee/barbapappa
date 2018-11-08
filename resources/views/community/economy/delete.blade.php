@extends('layouts.app')

@section('content')

    <h2 class="ui header">{{ $member->name }}</h2>
    <p>@lang('pages.communityMembers.deleteQuestion')</p>

    <div class="ui warning message visible">
        <span class="halflings halflings-warning-sign"></span>
        @lang('misc.cannotBeUndone')
    </div>

    {{-- TODO: toggle to also remove user from community bars --}}

    <br />

    {!! Form::open(['action' => ['CommunityMemberController@doDelete', 'communityId' => $community->human_id, 'memberId' => $member->id], 'method' => 'DELETE', 'class' => 'ui form']) !!}
        <div class="ui buttons">
            <a href="{{ route('community.member.show', ['communityId' => $community->human_id, 'memberId' => $member->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesRemove')</button>
        </div>
    {!! Form::close() !!}

@endsection
