@extends('layouts.app')

@php
    use \App\Http\Controllers\EconomyController;

    $economies = $community->economies()->get();
@endphp

@section('content')
    <h2 class="ui header">@lang('pages.economies.title') ({{ count($economies) }})</h2>
    <p>@lang('pages.economies.description')</p>

    <div class="ui vertical menu fluid">
        {{--
            <div class="item">
                <div class="ui transparent icon input">
                    <input type="text" placeholder="Search communities...">
                    <i class="icon glyphicons glyphicons-search link"></i>
                </div>
            </div>
        --}}

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

    <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.goTo')
    </a>
@endsection
