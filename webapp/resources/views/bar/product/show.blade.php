@extends('layouts.app')

@section('title', $product->displayName())

@php
    use \App\Http\Controllers\CommunityController;
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            in
            <a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}">
                {{ $bar->name }}
            </a>
        </div>
    </h2>

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
                        {{-- TODO: use style for this --}}
                        <i style="color: red;">@lang('misc.none')</i>
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
                    <input type="submit"
                            class="ui button blue"
                            value="@lang('pages.bar.quickBuy')">
                    <div class="ui basic left pointing blue label">
                        {{ $format_price }}
                    </div>
                </div>
            {!! Form::close() !!}
        </p>
    @endif

    </p>
        {{-- @if(perms(CommunityController::permsManage())) --}}
        {{--     <p> --}}
        {{--         <div class="ui buttons"> --}}
        {{--             <a href="{{ route('community.economy.product.edit', [ --}}
        {{--                 'communityId' => $community->human_id, --}}
        {{--                 'economyId' => $economy->id, --}}
        {{--                 'productId' => $product->id --}}
        {{--             ]) }}" --}}
        {{--                     class="ui button secondary"> --}}
        {{--                 @lang('misc.edit') --}}
        {{--             </a> --}}
        {{--             <a href="{{ route('community.economy.product.delete', [ --}}
        {{--                 'communityId' => $community->human_id, --}}
        {{--                 'economyId' => $economy->id, --}}
        {{--                 'productId' => $product->id --}}
        {{--             ]) }}" --}}
        {{--                     class="ui button negative"> --}}
        {{--                 @lang('misc.delete') --}}
        {{--             </a> --}}
        {{--         </div> --}}
        {{--     </p> --}}
        {{-- @endif --}}

        <a href="{{ route('bar.product.index', ['barId' => $bar->human_id]) }}"
                class="ui button basic">
            @lang('pages.products.backToProducts')
        </a>

        @if(perms(CommunityController::permsManage()))
            <a href="{{ route('community.economy.product.show', [
                'communityId' => $bar->community_id,
                'economyId' => $bar->economy_id,
                'productId' => $product->id,
            ]) }}"
                    class="ui button basic">
                @lang('pages.products.manageProduct')
            </a>
        @endif
    </p>
@endsection