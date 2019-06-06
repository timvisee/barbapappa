@extends('layouts.app')

@section('title', __('pages.paymentService.title'))

@php
    use \App\Http\Controllers\PaymentServiceController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.economies.backToEconomy'),
        'link' => route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.for')
            <a href="{{ route('community.economy.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                    ]) }}">
                {{ $economy->name }}
            </a>
        </div>
    </h2>

    {{-- Payment service list --}}
    @php
        $groups = [];
        if($services->contains('enabled', true))
            $groups[] = [
                'header' => trans_choice(
                    'pages.paymentService.enabledServices#',
                    $services->where('enabled', true)->count()
                ),
                'services' => $services->where('enabled', true),
            ];
        if($services->contains('enabled', false))
            $groups[] = [
                'header' => trans_choice(
                    'pages.paymentService.disabledServices#',
                    $services->where('enabled', false)->count()
                ),
                'services' => $services->where('enabled', false),
            ];
        if(empty($groups))
            $groups[] = ['services' => []];
    @endphp
    @include('community.economy.paymentservice.include.list', [
        'groups' => $groups,
    ])

    <p>
        @if(perms(PaymentServiceController::permsManage()))
            <a href="{{ route('community.economy.payservice.create', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                    class="ui button basic positive">
                @lang('misc.add')
            </a>
        @endif

        <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
                class="ui button basic">
            @lang('pages.economies.backToEconomy')
        </a>
    </p>
@endsection
