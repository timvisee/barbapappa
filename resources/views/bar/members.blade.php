@extends('layouts.app')

@php
    use \App\Perms\BarRoles;

    // Get all bar members
    $members = $bar->users()->get();
@endphp

@section('content')
    {{-- <h2 class="ui header">@lang('pages.bar.editBar')</h2> --}}
    <h2 class="ui header">BAR MEMBERS ({{ count($members) }})</h2>

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
            {{-- TODO: link to proper edit page --}}
            <a href="{{ route('bar.show', ['barId' => $bar->human_id ]) }}" class="item">
                {{ $member->name }}
                @if($member->role > BarRoles::USER)
                    {{-- TODO: show role name --}}
                    (role id: {{ $member->role }})
                @endif
            </a>
        @empty
            <div class="item">
                {{-- TODO: no members --}}
                <i>@lang('pages.bar.noBars')</i>
            </div>
        @endforelse
    </div>

    <br />

    <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
            class="ui button basic">
        {{-- TODO: go back button --}}
        @lang('general.cancel')
    </a>
@endsection
