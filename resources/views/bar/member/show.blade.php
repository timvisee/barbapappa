@extends('layouts.app')

@php
    use \App\Perms\BarRoles;
@endphp

@section('content')
    <h2 class="ui header">{{ $member->name }}</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>User</td>
                {{-- <td><a href="{{ route('community.show', ['communityId' => $community->human_id]) }}">{{ $community->id }}</a></td> --}}
                <td>{{ $member->name }}</td>
            </tr>
            <tr>
                <td>Role</td>
                <td>{{ BarRoles::roleName($member->pivot->role) }}</td>
            </tr>
            <tr>
                <td>Member since</td>
                <td>{{ $member->pivot->created_at }}</td>
            </tr>
            {{-- <tr> --}}
            {{--     <td>Slug</td> --}}
            {{--     @if($community->hasSlug()) --}}
            {{--         <td><a href="{{ route('community.show', ['communityId' => $community->slug]) }}">{{ $community->slug }}</a></td> --}}
            {{--     @else --}}
            {{--         <td><i>None</i></td> --}}
            {{--     @endif --}}
            {{-- </tr> --}}
        </tbody>
    </table>

    <br />

    <a href="{{ route('bar.member.index', ['barId' => $bar->human_id]) }}"
            class="ui button basic">
        @lang('general.goBack')
    </a>
@endsection
