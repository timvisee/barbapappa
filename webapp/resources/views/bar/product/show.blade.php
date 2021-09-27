@extends('layouts.app')

@section('title', $product->displayName())
@php
    $breadcrumbs = Breadcrumbs::generate('bar.product.show', $bar, $product);
    $menusection = 'bar';

    use App\Perms\CommunityRoles;
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

    <p>
        @if(($format_price = $product->formatPrice($currencies, BALANCE_FORMAT_PLAIN)) != null)
            {!! Form::open(['action' => [
                'BarController@quickBuy',
                $bar->human_id,
            ], 'method' => 'POST', 'class' => 'ui inline form']) !!}
                {!! Form::hidden('product_id', $product->id) !!}

                <div class="ui labeled button" tabindex="0">
                    {{ Form::submit(__('pages.bar.quickBuy'), ['class' => 'ui button blue']) }}
                    <div class="ui basic left pointing blue label">
                        {{ $format_price }}
                    </div>
                </div>
            {!! Form::close() !!}
        @endif

        @if(perms(CommunityRoles::presetManager()))
            <div class="ui floating right labeled icon dropdown button">
                <i class="dropdown icon"></i>
                @lang('misc.admin')
                <div class="menu">
                    <a href="{{ route('community.economy.product.show', ['communityId' => $community->human_id, 'economyId' => $product->economy->id, 'productId' => $product->id]) }}" class="item">
                        @lang('misc.show')
                    </a>
                    <a href="{{ route('community.economy.product.edit', ['communityId' => $community->human_id, 'economyId' => $product->economy->id, 'productId' => $product->id]) }}"
                            class="item">
                        @lang('misc.edit')
                    </a>
                    <a href="{{ route('community.economy.product.delete', ['communityId' => $community->human_id, 'economyId' => $product->economy->id, 'productId' => $product->id]) }}"
                            class="item">
                        @lang('misc.delete')
                    </a>
                </div>
            </div>
        @endif
    </p>

    <p>
        <a href="{{ route('bar.product.index', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.products.backToProducts')
        </a>
    </p>
@endsection
