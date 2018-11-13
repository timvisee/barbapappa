@extends('layouts.app')

@php
    use \App\Http\Controllers\BarMemberController;
    use \App\Perms\BarRoles;
@endphp

@section('content')
    <h2 class="ui header">{{ $member->name }}</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.user')</td>
                <td>{{ $member->name }}</td>
            </tr>
            <tr>
                <td>@lang('misc.role')</td>
                <td>{{ BarRoles::roleName($member->pivot->role) }}</td>
            </tr>
            @if($member->pivot->visited_at != null)
                <tr>
                    <td>@lang('pages.barMembers.lastVisit')</td>
                    <td>{{ $member->pivot->visited_at }}</td>
                </tr>
            @endif
            <tr>
                <td>@lang('pages.barMembers.memberSince')</td>
                <td>{{ $member->pivot->created_at }}</td>
            </tr>
            @if($member->pivot->created_at != $member->pivot->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>{{ $member->pivot->updated_at }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(perms(BarMemberController::permsManage()))
        <p>
            <div class="ui buttons">
                <a href="{{ route('bar.member.edit', ['barId' => $bar->human_id, 'memberId' => $member->id]) }}"
                        class="ui button secondary">
                    @lang('misc.edit')
                </a>
                <a href="{{ route('bar.member.delete', ['barId' => $bar->human_id, 'memberId' => $member->id]) }}"
                        class="ui button negative">
                    @lang('misc.delete')
                </a>
            </div>
        </p>
    @endif

    <p>
        <a href="{{ route('bar.member.index', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
