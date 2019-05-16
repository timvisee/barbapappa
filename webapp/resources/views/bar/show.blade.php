@extends('layouts.app')

@section('title', $bar->name)

@php
    use \App\Http\Controllers\BarController;
    use \App\Http\Controllers\BarMemberController;

    // Define menulinks
    $menulinks[] = [
        'name' => __('misc.information'),
        'link' => route('bar.info', ['barId' => $bar->human_id]),
        'icon' => 'info-sign',
    ];

    if(perms(BarController::permsUser()))
        $menulinks[] = [
            'name' => __('pages.stats.title'),
            'link' => route('bar.stats', ['barId' => $bar->human_id]),
            'icon' => 'stats',
        ];

    if(perms(BarMemberController::permsView()))
        $menulinks[] = [
            'name' => __('misc.members'),
            'link' => route('bar.member.index', ['barId' => $bar->human_id]),
            'icon' => 'user-structure',
        ];

    if($joined)
        $menulinks[] = [
            'name' => __('pages.wallets.yourWallets'),
            'link' => route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $bar->economy_id]),
            'icon' => 'wallet',
        ];

    if(perms(BarController::permsManage()))
        $menulinks[] = [
            'name' => __('pages.bar.editBar'),
            'link' => route('bar.edit', ['barId' => $bar->human_id]),
            'icon' => 'edit',
        ];

    $menulinks[] = [
        'name' => __('pages.community.viewCommunity'),
        'link' => route('community.show', ['communityId' => $community->human_id]),
        'icon' => 'group',
    ];
@endphp

@section('content')
    @include('bar.include.barHeader')
    @include('bar.include.joinBanner')

    <div class="ui vertical menu fluid">
        {!! Form::open(['action' => ['BarController@show', $bar->human_id], 'method' => 'GET', 'class' => 'ui form']) !!}
            <div class="item">
                <div class="ui transparent icon input">
                    {{ Form::text('q', Request::input('q'), [
                        'placeholder' => __('pages.products.search') . '...',
                    ]) }}
                    {{-- TODO: remove icon class? --}}
                    <i class="icon glyphicons glyphicons-search link"></i>
                </div>
            </div>
        {!! Form::close() !!}

        @forelse($products as $product)
            {!! Form::open(['action' => [
                'BarController@quickBuy',
                $bar->human_id,
            ], 'method' => 'POST']) !!}
                {!! Form::hidden('product_id', $product->id) !!}
                <a href="#" onclick="event.preventDefault();this.parentNode.submit()" class="item">
                    {{ $product->displayName() }}
                    {!! $product->formatPrice($currencies, BALANCE_FORMAT_LABEL) !!}
                </a>
            {!! Form::close() !!}
        @empty
            <i class="item">@lang('pages.products.noProducts')</i>
        @endforelse

        <a href="{{ route('bar.product.index', ['barId' => $bar->human_id]) }}"
                class="ui bottom attached button">
            @lang('misc.showAll')
        </a>
    </div>
@endsection
