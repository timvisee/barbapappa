@extends('layouts.app')

@section('title', $member->name)
@php
    $menusection = 'community_manage';

    use App\Perms\CommunityRoles;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open(['action' => [
        'CommunityMemberController@doEdit',
        $community->human_id,
        $member->id,
    ], 'method' => 'PUT', 'class' => 'ui form']) !!}
        <div class="field {{ ErrorRenderer::hasError('role') ? 'error' : '' }}">
            {{ Form::label('role', __('misc.role')) }}

            <div class="ui fluid selection dropdown">
                {{ Form::hidden('role', $member->role) }}
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.pleaseSpecify')</div>
                <div class="menu">
                    @foreach(CommunityRoles::roles() as $id => $name)
                        <div class="item" data-value="{{ $id }}">{{ $name }}</div>
                    @endforeach
                </div>
            </div>

            {{ ErrorRenderer::inline('role') }}
        </div>

        {{-- Show warning for modifying roles in general --}}
        <div class="ui top attached warning message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('pages.communityMembers.incorrectMemberRoleWarning')
        </div>

        {{-- Show warning for modifying own role --}}
        @if($member->user_id == barauth()->getSessionUser()->id)
            <div class="ui attached warning message visible">
                <span class="halflings halflings-warning-sign"></span>
                @lang('pages.communityMembers.ownRoleDowngradeWarning')
            </div>
        @endif

        {{-- Role change confirmation checkbox --}}
        <div class="ui bottom attached segment">
            <div class="inline required field {{ ErrorRenderer::hasError('confirm_role_change') ? 'error' : '' }}">
                <div class="ui checkbox">
                    {{ Form::checkbox('confirm_role_change', true, false, ['tabindex' => 0, 'class' => 'hidden']) }}
                    {{ Form::label('confirm_role_change', __('pages.barMembers.confirmRoleChange')) }}
                </div>
                <br />
                {{ ErrorRenderer::inline('confirm_role_change') }}
            </div>
        </div>

        <button class="ui button primary" type="submit">@lang('misc.saveChanges')</button>
        <a href="{{ route('community.member.show', [
            'communityId' => $community->human_id,
            'memberId' => $member->id,
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>
    {!! Form::close() !!}
@endsection
