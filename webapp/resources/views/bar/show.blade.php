@extends('layouts.app')

@section('title', $bar->name)
@php
    $breadcrumbs = Breadcrumbs::generate('bar.show', $bar);
    $menusection = 'bar';

    use \App\Http\Controllers\BarController;
@endphp

@push('scripts')
    <script type="text/javascript">
        // Provide API base url to client-side buy widget
        var barapp_quickbuy_api_url = '{{ route("bar.buy.api", ["barId" => $bar->human_id]) }}';
    </script>

    <script type="text/javascript" src="{{ mix('js/widget/quickbuy.js') }}" async></script>
@endpush

@section('content')
    {{-- Low balance message --}}
    @if(isset($userBalance) && $userBalance->amount < 0 && !empty($bar->low_balance_text))
        <div class="ui error message">
            <span class="halflings halflings-exclamation-sign icon"></span>
            {!! nl2br(e($bar->low_balance_text)) !!}
            <a href="{{ route('community.wallet.quickTopUp', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id
            ]) }}">@lang('pages.wallets.topUpNow')</a>.
        </div>
    @endif

    @include('bar.include.barHeader')
    @include('bar.include.joinBanner')

    <div class="ui two item menu">
        <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}" class="item active">@lang('pages.bar.buy.forMe')</a>
        <a href="{{ route('bar.buy', ['barId' => $bar->human_id]) }}" class="item">@lang('pages.bar.buy.forOthers')</a>
    </div>

    {{-- Quick buy list --}}
    <div id="quickbuy" class="ui large vertical menu fluid">
        {!! Form::open(['action' => ['BarController@show', $bar->human_id], 'method' => 'GET']) !!}
            <div class="item">
                <div class="ui transparent icon input">
                    {{ Form::text('q', Request::input('q'), [
                        'id' => 'quickbuy-search',
                        'placeholder' => __('pages.products.clickBuyOrSearch') . '...',
                    ]) }}
                    <i class="icon link">
                        <span class="glyphicons glyphicons-search"></span>
                    </i>
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

        <a href="{{ route('bar.product.index', ['barId' => $bar->human_id]) }}"
                class="ui large bottom attached button">
            @lang('pages.products.all')...
        </a>
    </div>

    {{-- Recently bought products list --}}
    @if($productMutations->isNotEmpty())
        <div class="ui large top vertical menu fluid">
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

                @if($linkTransaction || $linkProduct)
                    </a>
                @else
                    </div>
                @endif
            @endforeach

            @if(perms(BarController::permsManage()))
                <a href="{{ route('bar.history', ['barId' => $bar->human_id]) }}"
                        class="ui large bottom attached button">
                    @lang('pages.bar.allPurchases')...
                </a>
            @endif
        </div>
    @endif
@endsection
