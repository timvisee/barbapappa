@extends('layouts.app')

@section('title', $bar->name)

@php
    use \App\Http\Controllers\BarController;
    use \App\Http\Controllers\BarMemberController;
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

    <br />

    <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.viewCommunity')
    </a>

    @if(perms(BarController::permsUser()))
        <a href="{{ route('bar.info', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.barInfo')
        </a>

        <a href="{{ route('bar.stats', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.stats.title')
        </a>
    @endif

    @if(perms(BarMemberController::permsView()))
        <a href="{{ route('bar.member.index', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.barMembers.title')
        </a>
    @endif

    @if($joined)
        <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $bar->economy_id]) }}"
                class="ui button basic">
            @lang('pages.wallets.yourWallets')
        </a>
    @endif

    @if(perms(BarController::permsManage()))
        <a href="{{ route('bar.edit', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.bar.editBar')
        </a>
    @endif
@endsection
