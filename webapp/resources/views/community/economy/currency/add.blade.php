@extends('layouts.app')

@section('title', __('pages.currencies.createCurrency'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <div class="ui vertical menu fluid">
        <h5 class="ui item header">@lang('misc.presets')</h5>
        @foreach($presets as $code => $data)
            @if(!$data['exists'])
                <a href="{{ route('community.economy.currency.addPreset', ['communityId' => $community->human_id, 'economyId' => $economy->id, 'code' => $code]) }}" class="item">
                    {{ $data['name'] }}: {{ $data['symbol'] }}
                </a>
            @else
                <div class="item disabled">{{ $data['name'] }}: {{ $data['symbol'] }}</div>
            @endif
        @endforeach
        <a href="{{ route('community.economy.currency.create', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="ui bottom attached button">
            @lang('pages.currencies.createCustomCurrency')
        </a>
    </div>

    <a href="{{ route('community.economy.currency.index', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
            class="ui button basic">
        @lang('general.cancel')
    </a>
@endsection
