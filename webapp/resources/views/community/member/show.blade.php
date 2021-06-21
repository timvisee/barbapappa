@extends('layouts.app')

@section('title', $member->name)
@php
    $breadcrumbs = Breadcrumbs::generate('community.member.show', $member);
@endphp

@php
    use \App\Http\Controllers\CommunityMemberController;
    use \App\Perms\CommunityRoles;
    use \Carbon\Carbon;

    // Define menulinks
    $menulinks[] = [
        'name' => __('general.goBack'),
        'link' => route('community.member.index', ['communityId' => $community->human_id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.user')</td>
                <td>{{ $member->name }}</td>
            </tr>
            <tr>
                <td>@lang('misc.role')</td>
                <td>{{ CommunityRoles::roleName($member->role) }}</td>
            </tr>
            @if($member->visited_at != null)
                <tr>
                    <td>@lang('pages.communityMembers.lastVisit')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => new Carbon($member->visited_at)])</td>
                </tr>
            @endif
            <tr>
                <td>@lang('pages.communityMembers.memberSince')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $member->created_at])</td>
            </tr>
            @if($member->created_at != $member->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $member->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(perms(CommunityMemberController::permsManage()))
        <p>
            <div class="ui buttons">
                <a href="{{ route('community.member.edit', [
                    'communityId' => $community->human_id,
                    'memberId' => $member->id,
                ]) }}"
                        class="ui button secondary">
                    @lang('misc.edit')
                </a>
                <a href="{{ route('community.member.delete', [
                    'communityId' => $community->human_id,
                    'memberId' => $member->id,
                ]) }}"
                        class="ui button negative">
                    @lang('misc.delete')
                </a>
            </div>
        </p>
    @endif

    <a href="{{ route('community.member.index', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('general.goBack')
    </a>
@endsection
