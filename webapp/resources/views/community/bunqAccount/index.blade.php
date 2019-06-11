@extends('layouts.app')

@section('title', __('pages.bunqAccounts.title'))

@php
    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.community.goTo'),
        'link' => route('community.show', ['communityId' => $community->human_id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title') ({{ count($accounts) }})

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}">
                {{ $community->name }}
            </a>
        </div>
    </h2>

    {{-- TODO: change translation --}}
    <p>@lang('pages.bunqAccounts.description')</p>

    <div class="ui vertical menu fluid">
        @forelse($accounts as $account)
            {{-- TODO: link to bunq account page --}}
            <a href="{{ route('community.bunqAccount.show', [
                'communityId' => $community->human_id,
                'accountId' => $account->id
            ]) }}" class="item">
                {{ $account->name }}
            </a>
        @empty
            <div class="item">
                {{-- TODO: translate --}}
                <i>@lang('pages.bunqAccounts.noAccounts')</i>
            </div>
        @endforelse
    </div>

    {{-- TODO: check whether user can add new bunq account --}}
    <a href="{{ route('community.bunqAccount.create', ['communityId' => $community->human_id]) }}"
            class="ui button basic positive">
        @lang('misc.add')
    </a>

    <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.backToCommunity')
    </a>
@endsection
