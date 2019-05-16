@extends('layouts.app')

@section('title', __('pages.stats.barStats'))

@php
    use \App\Http\Controllers\BarMemberController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.bar.backToBar'),
        'link' => route('bar.show', ['barId' => $bar->human_id]),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.for')
            <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}">
                {{ $bar->name }}
            </a>
        </div>
    </h2>

    <h3 class="ui horizontal divider header">@lang('misc.bar')</h3>
    <div class="ui one small statistics">
        <div class="statistic">
            <div class="value">
                @include('includes.humanTimeDiff', ['time' => $bar->created_at, 'short' => true, 'absolute' => true])
            </div>
            <div class="label">@lang('misc.active')</div>
        </div>
    </div>

    <h3 class="ui horizontal divider header">
        @lang('misc.members')
    </h3>
    <div class="ui one small statistics">
        @if(perms(BarMemberController::permsView()))
            <a href="{{ route('bar.member.index', ['barId' => $bar->human_id]) }}"
                    class="statistic">
                <div class="value">{{ $bar->memberCount() }}</div>
                <div class="label">@lang('misc.enrolled')</div>
            </a>
        @else
            <div class="statistic">
                <div class="value">{{ $bar->memberCount() }}</div>
                <div class="label">@lang('misc.members')</div>
            </div>
        @endif
    </div>
    <div class="ui horizontal small statistics">
        <div class="statistic">
            <div class="value">{{ $memberCountHour }}</div>
            <div class="label">@lang('pages.stats.activeLastHour')</div>
        </div>
        <div class="statistic">
            <div class="value">{{ $memberCountDay }}</div>
            <div class="label">@lang('pages.stats.activeLastDay')</div>
        </div>
        <div class="statistic">
            <div class="value">{{ $memberCountMonth }}</div>
            <div class="label">@lang('pages.stats.activeLastMonth')</div>
        </div>
    </div>

    <h3 class="ui horizontal divider header">@lang('pages.products.title')</h3>
    <div class="ui one small statistics">
        <a href="{{ route('bar.product.index', ['barId' => $bar->human_id]) }}"
                class="statistic">
            <div class="value">{{ $productCount }}</div>
            <div class="label">@lang('misc.available')</div>
        </a>
    </div>

    <h3 class="ui horizontal divider header">@lang('misc.sold')</h3>
    <div class="ui two small statistics">
        <div class="statistic">
            <div class="value">{{ $soldProductCount }}</div>
            <div class="label">@lang('pages.products.title')</div>
        </div>
        <div class="statistic">
            <div class="value">{{ $transactionCount }}</div>
            <div class="label">@lang('pages.transactions.title')</div>
        </div>
    </div>
    <div class="ui horizontal small statistics">
        <div class="statistic">
            <div class="value">{{ $soldProductCountHour }}</div>
            <div class="label">@lang('pages.stats.productsLastHour')</div>
        </div>
        <div class="statistic">
            <div class="value">{{ $soldProductCountDay }}</div>
            <div class="label">@lang('pages.stats.productsLastDay')</div>
        </div>
        <div class="statistic">
            <div class="value">{{ $soldProductCountMonth }}</div>
            <div class="label">@lang('pages.stats.productsLastMonth')</div>
        </div>
    </div>

    <div class="ui divider hidden"></div>

    <p>
        <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.backToBar')
        </a>
    </p>
@endsection
