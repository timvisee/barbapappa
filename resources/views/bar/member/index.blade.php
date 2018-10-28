@extends('layouts.app')

@php
    use \App\Perms\BarRoles;

    // Get all bar members
    $members = $bar->users()->get();
@endphp

@section('content')
    <h2 class="ui header">@lang('pages.barMembers.title') ({{ count($members) }})</h2>
    <p>@lang('pages.barMembers.description')</p>

    <div class="ui vertical menu fluid">
        {{--
            <div class="item">
                <div class="ui transparent icon input">
                    <input type="text" placeholder="Search bars...">
                    <i class="icon glyphicons glyphicons-search link"></i>
                </div>
            </div>
        --}}

        @forelse($members as $member)
            <a href="{{ route('bar.member.show', ['barId' => $bar->human_id, 'memberId' => $member->id]) }}" class="item">
                {{ $member->name }}
                @if($member->pivot->role != 0)
                    ({{ BarRoles::roleName($member->pivot->role) }})
                @endif
            </a>
        @empty
            <div class="item">
                <i>@lang('pages.barmembers.noMembers')</i>
            </div>
        @endforelse
    </div>

    <br />

    <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
            class="ui button basic">
        @lang('general.goBack')
    </a>
@endsection
