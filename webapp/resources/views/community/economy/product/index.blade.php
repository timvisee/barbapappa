@extends('layouts.app')

@section('title', __('pages.products.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.economy.product.index', $economy);
    $menusection = 'community_manage';

    use App\Http\Controllers\ProductController;
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {{-- Product list menu --}}
    <div class="ui top menu fluid top attached">
        <div class="vertically fitted item borderless">
            <h5>@yield('title') ({{ $products->count() }})</h5>
        </div>
        <div class="right menu">
            <a href="?trashed={{ !$trashed }}" class="item{{ $trashed ?  ' active' : '' }}">
                @lang('misc.trashed')
            </a>
        </div>
    </div>

    {{-- Search field --}}
    <div class="ui vertical menu fluid attached">
        {!! Form::open(['action' => [
            'ProductController@index',
            'communityId' => $economy->community_id,
            'economyId' => $economy->id,
        ], 'method' => 'GET', 'class' => 'ui form']) !!}
            <div class="item">
                <div class="ui transparent icon input">
                    {{ Form::search('q', Request::input('q'), [
                        'placeholder' => __('pages.products.search') . '...',
                    ]) }}
                    <i class="icon link">
                        <span class="glyphicons glyphicons-search"></span>
                    </i>
                </div>
            </div>
        {!! Form::close() !!}
    </div>

    {{-- Product list --}}
    @include('community.economy.product.include.list', [
        'class' => ['bottom' , 'attached'],
        'groups' => [[
            'products' => $products,
        ]],
    ])

    <p>
        @if(perms(ProductController::permsManage()))
            <a href="{{ route('community.economy.product.create', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
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
