@extends('layouts.app')

@section('title', empty($search) ? __('pages.barMembers.title') : __('pages.barMembers.search') . ': ' . $search)
@php
    $breadcrumbs = Breadcrumbs::generate('bar.member.index', $bar);
    $menusection = 'bar_manage';

    use App\Perms\BarRoles;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.barMembers.description')</p>

    <div class="ui vertical menu fluid">
        {!! Form::open(['method' => 'GET']) !!}
            <div class="item">
                <div class="ui transparent icon input">
                    {{ Form::search('q', Request::input('q'), ['placeholder' => __('pages.barMembers.search') . '...']) }}
                    <i class="icon link">
                        <span class="glyphicons glyphicons-search"></span>
                    </i>
                </div>
            </div>
        {!! Form::close() !!}

        @forelse($members as $member)
            <a href="{{ route('bar.member.show', [
                'barId' => $bar->human_id,
                'memberId' => $member->id,
            ]) }}" class="item">
                {{ $member->name }}
                @if($member->role != 0)
                    ({{ BarRoles::roleName($member->role) }})
                @endif

                @if($member->visited_at)
                    <span class="sub-label">
                        @include('includes.humanTimeDiff', ['time' => $member->visited_at])
                    </span>
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
