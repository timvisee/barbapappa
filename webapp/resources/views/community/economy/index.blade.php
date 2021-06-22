@extends('layouts.app')

@section('title', __('pages.economies.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.index', $community);
    $menusection = 'community_manage';
@endphp

@php
    use \App\Http\Controllers\EconomyController;

    // Get a list of economies
    $economies = $community->economies()->get();

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.community.backToCommunity'),
        'link' => route('community.manage', ['communityId' => $community->human_id]),
        'icon' => 'group',
    ];
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.economies.description')</p>

    <div class="ui vertical menu fluid">
        @forelse($economies as $economy)
            <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item">
                {{ $economy->name }}
            </a>
        @empty
            <div class="item">
                <i>@lang('pages.economies.noEconomies')</i>
            </div>
        @endforelse
    </div>

    @if(perms(EconomyController::permsManage()))
        <a href="{{ route('community.economy.create', ['communityId' => $community->human_id]) }}"
                class="ui button basic positive">
            @lang('misc.create')
        </a>
    @endif

    <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.backToCommunity')
    </a>
@endsection
