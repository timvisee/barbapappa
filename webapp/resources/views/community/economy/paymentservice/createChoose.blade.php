@extends('layouts.app')

@section('title', __('pages.paymentService.newService'))

@php
    use BarPay\Models\Service as PayService;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.paymentService.newChooseType')</p>

    <div class="ui top vertical menu fluid">
        @forelse(PayService::SERVICEABLES as $service)
            <a class="item"
                    href="{{ route('community.economy.payservice.create', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'serviceable' => $service,
                    ]) }}">
                {{ $service::name() }}
            </a>
        @empty
            {{-- TODO: redirect back with error instead --}}
            <i class="item">@lang('pages.paymentService.noServices')</i>
        @endforelse
    </div>

    <a href="{{ route('community.economy.payservice.index', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
            ]) }}"
            class="ui button basic">
        @lang('general.cancel')
    </a>
@endsection
