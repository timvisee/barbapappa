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

    if($joined)
        $menulinks[] = [
            'name' => __('pages.wallets.yourWallets'),
            'link' => route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $bar->economy_id]),
            'icon' => 'wallet',
        ];

    if(perms(BarController::permsManage()))
        $menulinks[] = [
            'name' => __('misc.manage'),
            'link' => route('bar.manage', ['barId' => $bar->human_id]),
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

    {{-- Quick buy list --}}
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
                    {!! $product->formatPrice($currencies, BALANCE_FORMAT_LABEL, ['neutral' => true]) !!}
                </a>
            {!! Form::close() !!}
        @empty
            <i class="item">@lang('pages.products.noProducts')</i>
        @endforelse

        <a href="{{ route('bar.buy', ['barId' => $bar->human_id]) }}"
                class="ui bottom attached button">
            {{-- TODO: translate --}}
            Advanced buy
        </a>

        <a href="{{ route('bar.product.index', ['barId' => $bar->human_id]) }}"
                class="ui bottom attached button">
            @lang('misc.showAll')
        </a>
    </div>

    <div class="ui divider hidden"></div>

    {{-- Recently bought products list --}}
    @if($productMutations->isNotEmpty())
        <div class="ui top vertical menu fluid">
            <h5 class="ui item header">
                {{ trans_choice('pages.products.recentlyBoughtProducts#', $productMutations->sum('quantity')) }}
            </h5>

            @foreach($productMutations as $productMutation)
                @php
                    $self = barauth()->getUser()->id == $productMutation->mutation->owner_id;
                    $linkTransaction = $self || perms(BarController::permsManage());
                    $linkProduct = $productMutation->product_id != null;
                @endphp

                @if($linkTransaction || $linkProduct)
                    <a class="item"
                        href="{{ $linkTransaction ? route('transaction.show', [
                            'transactionId' => $productMutation->mutation->transaction_id,
                        ]) : route('bar.product.show', [
                            'barId' => $bar->human_id,
                            'productId' => $productMutation->product_id,
                        ])}}">
                @else
                    <div class="item">
                @endif

                    @if($productMutation->quantity != 1)
                        <span class="subtle">{{ $productMutation->quantity }}×</span>
                    @endif

                    {{ ($product = $productMutation->product) ?  $product->displayName() : __('pages.products.unknownProduct') }}
                    {!! $productMutation->mutation->formatAmount(BALANCE_FORMAT_LABEL, [
                        'color' => $self,
                    ]) !!}

                    @if($productMutation->mutation->owner_id != null)
                        <span class="subtle">
                            @lang('misc.by') {{ $productMutation->mutation->owner->first_name }}
                        </span>
                    @endif

                    <span class="sub-label">
                        @include('includes.humanTimeDiff', ['time' => $productMutation->updated_at ?? $productMutation->created_at, 'short' => true])
                    </span>

                @if($linkTransaction)
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </div>
    @endif
@endsection
