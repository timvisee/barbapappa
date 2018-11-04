@extends('layouts.app')

@php
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
            <tr>
                <td>@lang('pages.barMembers.memberSince')</td>
                <td>{{ $member->pivot->created_at }}</td>
            </tr>
            @if($member->pivot->created_at != $member->pivot->created_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>{{ $member->pivot->updated_at }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <br />

    <a href="{{ route('bar.member.index', ['barId' => $bar->human_id]) }}"
            class="ui button basic">
        @lang('general.goBack')
    </a>
    <a href="{{ route('bar.member.edit', ['barId' => $bar->human_id, 'memberId' => $member->id]) }}"
            class="ui button basic secondary">
        {{-- TODO: translate --}}
        Change role
    </a>
    <a href="{{ route('bar.member.delete', ['barId' => $bar->human_id, 'memberId' => $member->id]) }}"
            class="ui button basic negative">
        {{-- TODO: translate --}}
        Remove member
    </a>
@endsection
