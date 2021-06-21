@extends('layouts.app')

@section('title', __('pages.barMembers.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('bar.member.index', $bar);
@endphp

@php
    use \App\Perms\BarRoles;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.bar.backToBar'),
        'link' => route('bar.show', ['barId' => $bar->human_id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.barMembers.description')</p>

    <div class="ui vertical menu fluid">
        @forelse($members as $member)
            <a href="{{ route('bar.member.show', [
                'barId' => $bar->human_id,
                'memberId' => $member->id,
            ]) }}" class="item">
                {{ $member->name }}
                @if($member->role != 0)
                    ({{ BarRoles::roleName($member->role) }})
                @endif
            </a>
        @empty
            <div class="item">
                <i>@lang('pages.barMembers.noMembers')</i>
            </div>
        @endforelse
    </div>
    {{ $members->links() }}

    <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}"
            class="ui button basic">
        @lang('pages.bar.backToBar')
    </a>
@endsection
