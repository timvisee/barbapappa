@extends('layouts.app')

@section('title', $product->displayName())
@php
    $breadcrumbs = Breadcrumbs::generate('bar.product.show', $bar, $product);
    $menusection = 'bar';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>
                    <div class="ui list">
                        <div class="item">{{ $product->name }}</div>
                        @foreach($product->names as $name)
                            @unless($name->isHiddenLanguage())
                                <div class="item">
                                    <i>{{ $name->languageName() }}:</i>
                                    {{ $name->name }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </td>
            </tr>
            <tr>
                <td>@lang('pages.products.prices')</td>
                <td>
                    @if($product->prices->isNotEmpty())
                        <div class="ui list">
                            @foreach($product->prices as $price)
                                <div class="item">{{ $price->formatPrice() }}</div>
                            @endforeach
                        </div>
                    @else
                        <i class="ui text red">@lang('misc.none')</i>
                    @endif
                </td>
            </tr>
            <tr>
                <td>@lang('misc.availableSince')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $product->created_at])</td>
            </tr>
            @if($product->created_at != $product->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $product->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(($format_price = $product->formatPrice($currencies, BALANCE_FORMAT_PLAIN)) != null)
        <p>
            {!! Form::open(['action' => [
                'BarController@quickBuy',
                $bar->human_id,
            ], 'method' => 'POST', 'class' => 'ui form']) !!}
                {!! Form::hidden('product_id', $product->id) !!}

                <div class="ui labeled button" tabindex="0">
                    {{ Form::submit(__('pages.bar.quickBuy'), ['class' => 'ui button blue']) }}
                    <div class="ui basic left pointing blue label">
                        {{ $format_price }}
                    </div>
                </div>
            {!! Form::close() !!}
        </p>
    @endif

    {{-- TODO: admin button to manage product --}}

    <a href="{{ route('bar.product.index', ['barId' => $bar->human_id]) }}"
            class="ui button basic">
        @lang('pages.products.backToProducts')
    </a>
@endsection
